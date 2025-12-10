#!/bin/bash
# VERIFICACIÓN DE INSTALACIÓN SELECT2
# Este script verifica que todos los archivos estén en su lugar

echo "🔍 Verificando instalación de Select2..."
echo ""

# Colores
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Contador
OK=0
ERROR=0

# Función para verificar archivo
check_file() {
    if [ -f "$1" ]; then
        echo -e "${GREEN}✅${NC} Archivo existe: $1"
        ((OK++))
    else
        echo -e "${RED}❌${NC} Archivo FALTA: $1"
        ((ERROR++))
    fi
}

echo "📁 Verificando archivos..."
echo ""

check_file "/home/rorschard/Documents/Java/Inforecicla/src/main/resources/static/js/PuntoECA/select2-centros.js"
check_file "/home/rorschard/Documents/Java/Inforecicla/src/main/resources/static/css/PuntoECA/select2-custom.css"
check_file "/home/rorschard/Documents/Java/Inforecicla/src/main/resources/templates/views/PuntoECA/puntoECA-layout.html"
check_file "/home/rorschard/Documents/Java/Inforecicla/src/main/resources/templates/views/PuntoECA/section-centros.html"

echo ""
echo "📚 Verificando documentación..."
echo ""

check_file "/home/rorschard/Documents/Java/Inforecicla/README_SELECT2.md"
check_file "/home/rorschard/Documents/Java/Inforecicla/LISTO_PARA_USAR.md"
check_file "/home/rorschard/Documents/Java/Inforecicla/SOLUCION_COMPLETA.md"
check_file "/home/rorschard/Documents/Java/Inforecicla/QUICK_START.md"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Resultado: ${GREEN}✅ $OK/8${NC} archivos encontrados"
if [ $ERROR -gt 0 ]; then
    echo "           ${RED}❌ $ERROR/8${NC} archivos faltantes"
fi
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if [ $ERROR -eq 0 ]; then
    echo ""
    echo -e "${GREEN}✅ ¡Todos los archivos están en su lugar!${NC}"
    echo ""
    echo "Próximos pasos:"
    echo "1. mvn clean && mvn spring-boot:run"
    echo "2. Navega a /punto-eca/[gestor]/[usuario]/centros"
    echo "3. Abre F12 y busca logs [Select2]"
    echo ""
    echo -e "${GREEN}¡Listo para usar! 🚀${NC}"
else
    echo ""
    echo -e "${RED}⚠️ Hay archivos faltantes. Contacta al equipo de soporte.${NC}"
fi

