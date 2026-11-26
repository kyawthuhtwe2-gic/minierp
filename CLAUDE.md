## Repository Context Rules

- Do not read or analyze any files under `guide/`.
- Exclude `guide/` from all searches and code context unless explicitly requested by the user.
- Always inspect and analyze files under `commands/` and `skills/` for every task.
- Read and apply all relevant command and skill instructions before starting work.
- Do not execute commands automatically unless requested or appropriate.
- Do not ask the user which commands or skills to load.

These rules apply to all tasks, prompts, commands, and skills unless the user explicitly overrides them.