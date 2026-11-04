<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/_comun.php';

header('Content-Type: application/json; charset=utf-8');

studioExigirMetodo('POST');
exigirUsuarioAutenticado();
exigirTokenCsrf();

try {
    $entrada = studioLeerJson();
    $codigoModelo = $entrada['modelo_codigo'] ?? null;
    $codigoTamano = $entrada['tamano_codigo'] ?? null;
    $seleccionPiezas = $entrada['piezas'] ?? [];
    $codigosExtras = $entrada['extras'] ?? [];
    $cantidad = filter_var($entrada['cantidad'] ?? 1, FILTER_VALIDATE_INT);

    if (!studioCodigoValido($codigoModelo) || !studioCodigoValido($codigoTamano)) {
        studioResponder(400, ['ok' => false, 'mensaje' => 'El modelo o tamaño no es válido.']);
    }
    if (!is_array($seleccionPiezas) || count($seleccionPiezas) > 30 || !is_array($codigosExtras) || count($codigosExtras) > 30) {
        studioResponder(400, ['ok' => false, 'mensaje' => 'Las opciones seleccionadas no son válidas.']);
    }
    if ($cantidad === false || $cantidad < 1 || $cantidad > 20) {
        studioResponder(400, ['ok' => false, 'mensaje' => 'La cantidad debe estar entre 1 y 20.']);
    }
    foreach ($seleccionPiezas as $pieza => $color) {
        if (!studioCodigoValido($pieza) || !studioCodigoValido($color)) {
            studioResponder(400, ['ok' => false, 'mensaje' => 'Hay una selección de color no válida.']);
        }
    }
    foreach ($codigosExtras as $extra) {
        if (!studioCodigoValido($extra)) {
            studioResponder(400, ['ok' => false, 'mensaje' => 'Hay un extra no válido.']);
        }
    }

    require_once __DIR__ . '/../conexion_mongodb.php';

    $modelo = $db->studio_modelos->findOne(['codigo' => $codigoModelo, 'activo' => true]);
    if (!$modelo) {
        studioResponder(404, ['ok' => false, 'mensaje' => 'El modelo no existe o no está disponible.']);
    }

    $tamanoPermitido = null;
    foreach (($modelo['tamanos'] ?? []) as $tamano) {
        if (($tamano['codigo'] ?? null) === $codigoTamano) {
            $tamanoPermitido = $tamano;
            break;
        }
    }
    if ($tamanoPermitido === null) {
        studioResponder(400, ['ok' => false, 'mensaje' => 'El tamaño no está permitido para este modelo.']);
    }

    $piezasPermitidas = [];
    foreach (($modelo['piezas'] ?? []) as $pieza) {
        $piezaCodigo = $pieza['codigo'] ?? null;
        if (!is_string($piezaCodigo)) continue;
        $colores = [];
        foreach (($pieza['colores'] ?? []) as $color) {
            if (isset($color['codigo']) && is_string($color['codigo'])) $colores[] = $color['codigo'];
        }
        $piezasPermitidas[$piezaCodigo] = $colores;
    }
    foreach ($seleccionPiezas as $piezaCodigo => $colorCodigo) {
        if (!isset($piezasPermitidas[$piezaCodigo]) || !in_array($colorCodigo, $piezasPermitidas[$piezaCodigo], true)) {
            studioResponder(400, ['ok' => false, 'mensaje' => 'La combinación de piezas y colores no está permitida.']);
        }
    }

    $reglas = $db->studio_reglas_precio->findOne(
        ['modelo_codigo' => $codigoModelo, 'estado' => 'publicada'],
        ['sort' => ['version' => -1]]
    );
    if (!$reglas) {
        studioResponder(409, ['ok' => false, 'mensaje' => 'Este modelo todavía no tiene reglas de precio publicadas.']);
    }

    $multiplicadorTamano = $tamanoPermitido['multiplicador_consumo'] ?? 1;
    $margen = $reglas['margen_objetivo'] ?? null;
    $minutosManoObra = $reglas['mano_obra_minutos'] ?? null;
    $tarifaHora = $reglas['mano_obra_tarifa_hora_cop'] ?? null;
    $desperdicio = $reglas['desperdicio_porcentaje'] ?? 0;

    if (!is_numeric($multiplicadorTamano) || $multiplicadorTamano <= 0 || $multiplicadorTamano > 10 ||
        !is_numeric($margen) || $margen < 0 || $margen >= 0.8 ||
        !is_numeric($minutosManoObra) || $minutosManoObra < 0 || $minutosManoObra > 100000 ||
        !is_numeric($tarifaHora) || $tarifaHora < 0 || $tarifaHora > 100000000 ||
        !is_numeric($desperdicio) || $desperdicio < 0 || $desperdicio > 100) {
        studioResponder(500, ['ok' => false, 'mensaje' => 'Las reglas de precio del modelo están incompletas o son inválidas.']);
    }

    $costeMateriales = 0;
    $desgloseMateriales = [];
    foreach (($reglas['materiales'] ?? []) as $materialRegla) {
        $codigoMaterial = $materialRegla['codigo'] ?? null;
        $consumoBase = $materialRegla['cantidad_por_unidad'] ?? null;
        if (!studioCodigoValido($codigoMaterial) || !is_numeric($consumoBase) || $consumoBase < 0 || $consumoBase > 10000000) {
            studioResponder(500, ['ok' => false, 'mensaje' => 'Hay una regla de consumo de material inválida.']);
        }
        $material = $db->studio_materiales->findOne(['codigo' => $codigoMaterial, 'activo' => true]);
        if (!$material || !studioImporteValido($material['coste_por_unidad_cop'] ?? null)) {
            studioResponder(409, ['ok' => false, 'mensaje' => 'Falta configurar un material activo con coste válido.']);
        }
        $consumo = (float)$consumoBase * (float)$multiplicadorTamano;
        $coste = (int)ceil($consumo * (int)$material['coste_por_unidad_cop']);
        $costeMateriales += $coste;
        $desgloseMateriales[] = [
            'codigo' => $codigoMaterial,
            'unidad' => (string)($material['unidad'] ?? ''),
            'consumo_estimado' => $consumo,
            'coste_cop' => $coste
        ];
    }

    $costeDesperdicio = (int)ceil($costeMateriales * ((float)$desperdicio / 100));
    $costeManoObra = (int)ceil(((float)$minutosManoObra / 60) * (int)$tarifaHora);

    $extrasReglas = [];
    foreach (($reglas['extras'] ?? []) as $extraRegla) {
        if (isset($extraRegla['codigo'])) $extrasReglas[$extraRegla['codigo']] = $extraRegla;
    }
    $costeExtras = 0;
    $desgloseExtras = [];
    $codigosExtras = array_values(array_unique($codigosExtras));
    foreach ($codigosExtras as $extraCodigo) {
        $extra = $extrasReglas[$extraCodigo] ?? null;
        if (!is_array($extra) || !studioImporteValido($extra['coste_cop'] ?? null)) {
            studioResponder(400, ['ok' => false, 'mensaje' => 'Un extra no está permitido o no tiene coste válido.']);
        }
        $costeExtras += (int)$extra['coste_cop'];
        $desgloseExtras[] = ['codigo' => $extraCodigo, 'coste_cop' => (int)$extra['coste_cop']];
    }

    $costeBase = $costeMateriales + $costeDesperdicio + $costeManoObra + $costeExtras;
    if ($costeBase <= 0 || $costeBase > 1000000000) {
        studioResponder(409, ['ok' => false, 'mensaje' => 'El coste de fabricación debe configurarse antes de calcular el precio.']);
    }
    $precioUnitario = (int)ceil($costeBase / (1 - (float)$margen));
    $total = $precioUnitario * $cantidad;
    if ($total > 100000000000) {
        studioResponder(400, ['ok' => false, 'mensaje' => 'El importe calculado supera el límite permitido.']);
    }

    studioResponder(200, [
        'ok' => true,
        'moneda' => 'COP',
        'modelo_codigo' => $codigoModelo,
        'cantidad' => $cantidad,
        'version_reglas' => (int)($reglas['version'] ?? 1),
        'desglose' => [
            'materiales' => $desgloseMateriales,
            'coste_materiales_cop' => $costeMateriales,
            'coste_desperdicio_cop' => $costeDesperdicio,
            'coste_mano_obra_cop' => $costeManoObra,
            'extras' => $desgloseExtras,
            'coste_extras_cop' => $costeExtras,
            'coste_base_cop' => $costeBase,
            'margen_objetivo' => (float)$margen,
            'precio_unitario_cop' => $precioUnitario,
            'total_cop' => $total
        ],
        'nota' => 'Precio calculado con las reglas vigentes. La solicitud requiere revisión de fabricación.'
    ]);
} catch (Throwable $e) {
    error_log('Studio calcular precio: ' . $e->getMessage());
    studioResponder(500, ['ok' => false, 'mensaje' => 'No se pudo calcular el precio.']);
}
