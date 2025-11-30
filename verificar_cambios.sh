#!/bin/bash
# Script de verificación rápida del bug de excepciones

echo "╔════════════════════════════════════════════════════════════╗"
echo "║  VERIFICACIÓN: Mensaje de Excepción de Material Duplicado  ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Funciones de utilidad
print_check() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

echo ""
echo "1️⃣  VERIFICAR COMPILACIÓN"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if mvn clean compile -q -DskipTests 2>/dev/null; then
    print_check "Compilación exitosa"
else
    print_error "Error en compilación - Revisa los logs"
    exit 1
fi

echo ""
echo "2️⃣  VERIFICAR CAMBIOS APLICADOS"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Verificar InventarioServiceImpl
if grep -q "materialesQueYaExisten" src/main/java/org/sena/inforecicla/service/impl/InventarioServiceImpl.java; then
    print_check "InventarioServiceImpl.java actualizado (lógica explícita de conteo)"
else
    print_error "InventarioServiceImpl.java no contiene los cambios esperados"
fi

# Verificar logs de depuración
if grep -q "DEBUG buscarMaterial" src/main/java/org/sena/inforecicla/service/impl/InventarioServiceImpl.java; then
    print_check "Logs de depuración agregados"
else
    print_warning "Logs de depuración no encontrados (opcional)"
fi

# Verificar HTML mejorado
if grep -q "Response OK:" src/main/resources/templates/views/PuntoECA/section-materiales.html; then
    print_check "section-materiales.html actualizado (mejor manejo de errores)"
else
    print_error "section-materiales.html no contiene los cambios esperados"
fi

# Verificar uso de optional chaining
if grep -q "data?.mensaje" src/main/resources/templates/views/PuntoECA/section-materiales.html; then
    print_check "Optional chaining implementado en el frontend"
else
    print_warning "Optional chaining no encontrado (verificar manejo de errores)"
fi

echo ""
echo "3️⃣  ANÁLISIS ESTÁTICO"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Contar líneas de cambio
SERVICE_LINES=$(wc -l < src/main/java/org/sena/inforecicla/service/impl/InventarioServiceImpl.java)
print_info "InventarioServiceImpl.java: $SERVICE_LINES líneas"

HTML_LINES=$(wc -l < src/main/resources/templates/views/PuntoECA/section-materiales.html)
print_info "section-materiales.html: $HTML_LINES líneas"

echo ""
echo "4️⃣  VALIDAR ESTRUCTURA DEL PROYECTO"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Verificar que los archivos existen
if [ -f src/main/java/org/sena/inforecicla/service/impl/InventarioServiceImpl.java ]; then
    print_check "InventarioServiceImpl.java existe"
else
    print_error "InventarioServiceImpl.java no encontrado"
fi

if [ -f src/main/java/org/sena/inforecicla/controller/PuntoEcaController.java ]; then
    print_check "PuntoEcaController.java existe"
else
    print_error "PuntoEcaController.java no encontrado"
fi

if [ -f src/main/resources/templates/views/PuntoECA/section-materiales.html ]; then
    print_check "section-materiales.html existe"
else
    print_error "section-materiales.html no encontrado"
fi

echo ""
echo "5️⃣  DOCUMENTACIÓN"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if [ -f VERIFICACION_EXCEPCION_MATERIAL.md ]; then
    print_check "VERIFICACION_EXCEPCION_MATERIAL.md encontrado"
else
    print_warning "VERIFICACION_EXCEPCION_MATERIAL.md no encontrado"
fi

if [ -f ANALISIS_TECNICO_BUG.md ]; then
    print_check "ANALISIS_TECNICO_BUG.md encontrado"
else
    print_warning "ANALISIS_TECNICO_BUG.md no encontrado"
fi

echo ""
echo "6️⃣  PRÓXIMOS PASOS"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
print_info "Recomendaciones para validar:"
echo ""
echo "  1. Inicia la aplicación:"
echo "     ${BLUE}mvn spring-boot:run${NC}"
echo ""
echo "  2. Abre el navegador y ve a:"
echo "     ${BLUE}http://localhost:8080/punto-eca/{nombrePunto}/{gestorId}${NC}"
echo ""
echo "  3. Navega a la sección de 'Materiales'"
echo ""
echo "  4. Prueba estos escenarios:"
echo ""
echo "     a) Inventario VACÍO:"
echo "        - Haz clic en 'Agregar'"
echo "        - Selecciona un material y agrégalo"
echo "        - Repite: busca el mismo material"
echo "        - Esperado: ⚠️ Mensaje de error ${GREEN}✓${NC}"
echo ""
echo "     b) Buscar material EXISTENTE:"
echo "        - Escribe nombre en la búsqueda"
echo "        - El material ya existe en inventario"
echo "        - Esperado: ⚠️ Mensaje de error ${GREEN}✓${NC}"
echo ""
echo "     c) Verificar LOGS:"
echo "        - Abre la consola del navegador (F12)"
echo "        - Busca: 'Response OK: false Status: 400'"
echo "        - Busca el mensaje de error formateado ${GREEN}✓${NC}"
echo ""
echo "  5. Revisa los logs del servidor:"
echo "     ${BLUE}grep 'DEBUG buscarMaterial' <logs>${NC}"
echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║              ✓ VERIFICACIÓN COMPLETADA                    ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

