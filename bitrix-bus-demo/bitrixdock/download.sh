#!/bin/bash

set -euo pipefail

if [ $# -lt 2 ]; then
    echo "Использование: $0 <редакция> <лицензионный_ключ>"
    echo ""
    echo "Редакции:"
    echo "  business             Бизнес"
    echo "  small_business       Малый бизнес"
    echo "  standard             Стандарт"
    echo "  start                Старт"
    echo "  bitrix24             Корпоративный портал"
    echo "  bitrix24_enterprise  Энтерпрайз"
    echo "  bitrix24_shop        Интернет-магазин + CRM"
    exit 1
fi

EDITION="$1"
LICENSE_KEY="$2"
LP=$(echo -n "$LICENSE_KEY" | md5sum | cut -d' ' -f1)

URL="https://www.1c-bitrix.ru/private/download/${EDITION}_source.tar.gz?lp=${LP}"
OUTPUT="www/${EDITION}_source.tar.gz"

echo "Скачиваю ${EDITION}_source.tar.gz..."
curl --progress-bar -fSL "$URL" -o "$OUTPUT"
echo "Готово: $OUTPUT"
