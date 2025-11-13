"""Prompt-quality MCP server.

This module exposes a single MCP tool named ``submit_prompt_quality`` that will be
consumed by Cursor (or any other MCP-compatible client).  The tool receives a
prompt-quality score and forwards it to a Laravel backend.  The implementation
is intentionally kept minimal, but we still try to:

* Remain compatible with both *fastmcp* ≥ 2.9 (published as ``fastmcp``) and the
  legacy package distributed under ``mcp.server.fastmcp``.
* Fail fast with a helpful log message if either dependency is missing or if
  the outbound HTTP request fails.
"""

from __future__ import annotations

import asyncio
import logging
import os
from typing import Any

import httpx

# ---------------------------------------------------------------------------
# FastMCP import compatibility shim
# ---------------------------------------------------------------------------

try:
    # Preferred location since FastMCP ≥ 2.9
    from fastmcp import FastMCP  # type: ignore
except ModuleNotFoundError:  # pragma: no cover — runtime fallback
    # Legacy fallback that works for FastMCP ≤ 2.8.x
    from mcp.server.fastmcp import FastMCP  # type: ignore

# ---------------------------------------------------------------------------
# Logging setup
# ---------------------------------------------------------------------------

logging.basicConfig(
    level=os.getenv("LOGLEVEL", "INFO").upper(),
    format="[%(asctime)s] %(levelname)s %(name)s: %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S",
)
logger = logging.getLogger(__name__)

# ---------------------------------------------------------------------------
# MCP server & tool definition
# ---------------------------------------------------------------------------

mcp = FastMCP("prompt-quality")

# ▸ gunakan tanda kurung jika FastMCP ≥2.9 (lihat traceback Anda sebelumnya)
@mcp.tool()
async def submit_prompt_quality(
    project: str,
    efektivitas: int,
    membingungkan: int,
    ambiguous: int = 0,
    comments: str = "",
) -> str:
    """Kirim skor kualitas prompt ke endpoint Laravel.
    
    Args:
        project: Nama project
        efektivitas: Skor efektivitas (1-100)
        membingungkan: Skor membingungkan (1-100)
        ambiguous: Skor ambiguitas (1-100, optional)
        comments: Komentar tambahan (optional)
    """
    # Allow the caller to omit ``client_uuid`` and fall back to the environment
    # variable ``CLIENT_UUID`` (or ``default-uuid`` if not set).
    client_uuid = os.getenv("CLIENT_UUID")

    payload: dict[str, Any] = {
        "client_uuid": client_uuid,
        "project": project,
        "prompt_quality": {
            "efektivitas": efektivitas,
            "membingungkan": membingungkan,
            "ambiguous": ambiguous if ambiguous > 0 else None,
            "comments": comments if comments else None,
        },
    }

    base_url = os.getenv("PROMPT_QUALITY_API", "http://localhost:8000").rstrip("/")
    url = f"{base_url}/api/prompt-quality"

    logger.debug("POST %s • payload=%s", url, payload)

    try:
        async with httpx.AsyncClient(timeout=10) as client:
            resp = await client.post(
                url,
                json=payload,
                headers={
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                },
            )
            resp.raise_for_status()
    except httpx.HTTPError as exc:
        logger.error("Failed to POST to %s: %s", url, exc)
        raise

    logger.info("Prompt quality sent successfully • HTTP %s", resp.status_code)
    return f"✅ terkirim (HTTP {resp.status_code})"

if __name__ == "__main__":
    # Jalankan server MCP via STDIO — persis yang Cursor harapkan
    # The server is entirely asynchronous, but FastMCP provides a synchronous
    # ``run`` helper.  If you prefer to manage the event loop yourself, uncomment
    # the snippet below.

    # asyncio.run(mcp.serve_stdio())

    mcp.run()

