from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[2]
TEMPLATES_ROOT = ROOT / "backend" / "templates"
TRANSLATIONS_FILE = ROOT / "backend" / "translations" / "messages.fr.yaml"

translations = {}
used_keys = set()


def slugify(value: str) -> str:
    value = value.lower().strip()
    repl = {
        "à": "a",
        "â": "a",
        "ä": "a",
        "á": "a",
        "ã": "a",
        "ç": "c",
        "é": "e",
        "è": "e",
        "ê": "e",
        "ë": "e",
        "î": "i",
        "ï": "i",
        "ì": "i",
        "í": "i",
        "ô": "o",
        "ö": "o",
        "ò": "o",
        "ó": "o",
        "õ": "o",
        "ù": "u",
        "û": "u",
        "ü": "u",
        "ú": "u",
        "œ": "oe",
        "æ": "ae",
        "ñ": "n",
        "’": "",
        "'": "",
    }
    for a, b in repl.items():
        value = value.replace(a, b)
    value = re.sub(r"[^a-z0-9]+", "_", value).strip("_")
    return value or "text"


def add_translation(namespace: str, text: str) -> str | None:
    text = re.sub(r"\s+", " ", text).strip()
    if not text:
        return None
    if re.fullmatch(r"[\-–—_=+*/#?!.,:;()\[\]{}\s]+", text):
        return None
    if not re.search(r"[A-Za-zÀ-ÿ]", text):
        return None

    for key, val in translations.items():
        if val == text:
            return key

    base = f"{namespace}.{slugify(text)[:70]}"
    key = base
    i = 2
    while key in used_keys and translations.get(key) != text:
        key = f"{base}_{i}"
        i += 1
    translations[key] = text
    used_keys.add(key)
    return key


def sanitize_for_scan(content: str) -> str:
    content = re.sub(r"\{\#.*?\#\}", "", content, flags=re.S)
    content = re.sub(r"<style\b[^>]*>.*?</style>", "", content, flags=re.S | re.I)
    content = re.sub(r"<script\b[^>]*>.*?</script>", "", content, flags=re.S | re.I)
    return content


def split_emoji_prefix(text: str) -> tuple[str, str]:
    m = re.match(r"^([\U0001F300-\U0001FAFF\u2600-\u27BF]+\s*)(.+)$", text)
    if m:
        return m.group(1), m.group(2)
    return "", text


def replace_titles(content: str, namespace: str) -> str:
    pat_title = re.compile(r"(\{\%\s*block\s+title\s*\%\})(.*?)(\{\%\s*endblock\s*\%\})", re.S)

    def repl(match):
        text = re.sub(r"\s+", " ", match.group(2)).strip()
        if not text or "{{" in text or "{%" in text:
            return match.group(0)
        key = add_translation(namespace, text)
        if not key:
            return match.group(0)
        return f"{match.group(1)}{{{{ '{key}'|trans }}}}{match.group(3)}"

    return pat_title.sub(repl, content)


def replace_attributes(content: str, namespace: str) -> str:
    patterns = [
        re.compile(r'(placeholder\s*=\s*")(.*?)(")'),
        re.compile(r'(aria-label\s*=\s*")(.*?)(")'),
        re.compile(r'(alt\s*=\s*")(.*?)(")'),
    ]

    for pattern in patterns:
        def repl(match):
            text = match.group(2).strip()
            if not text or "{{" in text or "{%" in text:
                return match.group(0)
            key = add_translation(namespace, text)
            if not key:
                return match.group(0)
            return f"{match.group(1)}{{{{ '{key}'|trans }}}}{match.group(3)}"

        content = pattern.sub(repl, content)
    return content


def replace_text_nodes(content: str, namespace: str) -> str:
    node_pattern = re.compile(r">(.*?)<", re.S)

    def repl(match):
        raw = match.group(1)
        compact = re.sub(r"\s+", " ", raw).strip()
        if not compact:
            return match.group(0)
        if any(x in compact for x in ("{{", "}}", "{%", "%}", "{#", "#}")):
            return match.group(0)
        if compact in {"?", "---", "IG", "X", "FB", "—"}:
            return match.group(0)

        prefix, payload = split_emoji_prefix(compact)
        key = add_translation(namespace, payload)
        if not key:
            return match.group(0)
        return f">{prefix}{{{{ '{key}'|trans }}}}<"

    sanitized = sanitize_for_scan(content)
    replaced = []
    idx = 0
    for m in node_pattern.finditer(sanitized):
        start, end = m.span()
        replaced.append(content[idx:start])
        replaced.append(repl(re.match(r"(>)(.*?)(<)", content[start:end], re.S).group(0) and re.match(r">(.*?)<", content[start:end], re.S)))
        idx = end
    replaced.append(content[idx:])
    return "".join(replaced) if replaced else content


for twig_file in sorted(TEMPLATES_ROOT.rglob("*.twig")):
    content = twig_file.read_text(encoding="utf-8")
    original = content
    rel = twig_file.relative_to(TEMPLATES_ROOT).as_posix().replace("/", "_").replace(".twig", "")
    namespace = rel

    content = replace_titles(content, namespace)
    content = replace_attributes(content, namespace)

    # Safe direct node replacement without crossing Twig comments/logic.
    node_pattern = re.compile(r">(.*?)<", re.S)

    def safe_repl(match):
        raw = match.group(1)
        compact = re.sub(r"\s+", " ", raw).strip()
        if not compact:
            return match.group(0)
        if any(x in compact for x in ("{{", "}}", "{%", "%}", "{#", "#}")):
            return match.group(0)
        if compact in {"?", "---", "IG", "X", "FB", "—"}:
            return match.group(0)
        if "/*" in compact or "*/" in compact:
            return match.group(0)

        prefix, payload = split_emoji_prefix(compact)
        key = add_translation(namespace, payload)
        if not key:
            return match.group(0)
        return f">{prefix}{{{{ '{key}'|trans }}}}<"

    content = node_pattern.sub(safe_repl, content)

    if content != original:
        twig_file.write_text(content, encoding="utf-8")

TRANSLATIONS_FILE.parent.mkdir(parents=True, exist_ok=True)
lines = []
for key in sorted(translations):
    val = translations[key].replace('"', '\\"')
    lines.append(f'{key}: "{val}"')
TRANSLATIONS_FILE.write_text("\n".join(lines) + "\n", encoding="utf-8")

print("Updated Twig templates and messages.fr.yaml")
