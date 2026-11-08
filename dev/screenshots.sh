#!/usr/bin/env bash
# Regenerates docs/screenshots/*.png from the sample data with headless Chrome.
# Usage: dev/screenshots.sh   (needs php and google-chrome or chromium on PATH)
set -euo pipefail
cd "$(dirname "$0")/.."
chrome="$(command -v google-chrome || command -v chromium || command -v chromium-browser)"
tmp="$(mktemp -d)"
trap 'rm -rf "$tmp"' EXIT
mkdir -p docs/screenshots
for name in $(php dev/screenshots.php "$tmp"); do
	"$chrome" --headless=new --no-sandbox --hide-scrollbars --force-device-scale-factor=2 \
		--window-size=1000,1400 --virtual-time-budget=4000 \
		--screenshot="$tmp/$name.full.png" "file://$tmp/$name.html" 2>/dev/null
	python3 - "$tmp/$name.full.png" "docs/screenshots/$name.png" <<'PY'
import sys
from PIL import Image, ImageChops
im = Image.open(sys.argv[1]).convert("RGB")
bg = Image.new("RGB", im.size, im.getpixel((im.width - 1, im.height - 1)))
box = ImageChops.difference(im, bg).getbbox()
pad = 40
if box:
    box = (max(box[0] - pad, 0), max(box[1] - pad, 0), min(box[2] + pad, im.width), min(box[3] + pad, im.height))
    im = im.crop(box)
im.save(sys.argv[2], optimize=True)
PY
	echo "docs/screenshots/$name.png"
done
