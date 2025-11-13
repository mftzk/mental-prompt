import { McpServer } from "@modelcontextprotocol/sdk/server/mcp.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import { z } from "zod";
import axios from "axios";

// Inisialisasi MCP server
type ToolInput = {
  client_uuid: string;
  project: string;
  efektivitas: number;
  membingungkan: number;
};

const server = new McpServer({
  name: "prompt-quality",
  version: "1.0.0",
});

// Daftarkan tool submit_prompt_quality
server.registerTool(
  "submit_prompt_quality",
  {
    title: "Submit Prompt Quality",
    description: "Kirim skor kualitas prompt ke backend Laravel.",
    inputSchema: {
      client_uuid: z.string().uuid(),
      project: z.string(),
      efektivitas: z.number().min(0).max(100),
      membingungkan: z.number().min(0).max(100),
    },
  },
  async ({ client_uuid, project, efektivitas, membingungkan }: ToolInput) => {
    const payload = {
      client_uuid,
      project,
      prompt_quality: { efektivitas, membingungkan },
    };

    const base = process.env.PROMPT_QUALITY_API ?? "http://localhost:8000";
    const resp = await axios.post(`${base}/api/prompt-quality`, payload, {
      timeout: 10000,
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
      },
    });

    return {
      content: [
        {
          type: "text",
          text: `✅ Terkirim (HTTP ${resp.status})`,
        },
      ],
    } as const;
  }
);

// Jalankan server MCP via STDIO
async function main() {
  const transport = new StdioServerTransport();
  await server.connect(transport);
}

main().catch((err) => {
  console.error("Failed to start MCP server:", err);
  process.exit(1);
});

// Jalankan dengan:
//   npx tsx --esm prompt-quality-server.ts
// Atau jika menggunakan CommonJS mode:
//   npx tsx prompt-quality-server.ts  (tanpa top-level await di file ini)

