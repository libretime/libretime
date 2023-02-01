import re

VERSION_RE = re.compile(
    r"""
    (?P<release>[0-9]+(?:\.[0-9]+)*)
    (?:
        -(?P<pre_l>(alpha|beta))
        (?:
            \.(?P<pre_n>[0-9]+
                (?:
                    \.[0-9]+
                )?
            )
        )?
    )?
    """,
    re.VERBOSE | re.IGNORECASE,
)


def parse_version(version: str):
    match = VERSION_RE.search(version)
    if not match:
        raise ValueError(f"invalid version {version}")

    release = list(map(int, match.group("release").split(".")))
    major = release.pop(0) if release else 0
    minor = release.pop(0) if release else 0
    patch = release.pop(0) if release else 0

    pre_mapping = {"alpha": -2, "beta": -1, None: 0, "": 0}
    pre = pre_mapping[match.group("pre_l")]

    pre_major, pre_minor = 0, 0
    pre_version = match.group("pre_n")
    if pre_version:
        pre_version_list = pre_version.split(".")
        pre_major = int(pre_version_list.pop(0)) if len(pre_version_list) else 0
        pre_minor = int(pre_version_list.pop(0)) if len(pre_version_list) else 0

    return (major, minor, patch, pre, pre_major, pre_minor)
