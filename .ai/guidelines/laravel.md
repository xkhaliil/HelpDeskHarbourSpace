This is a demo project for a web development basics course. 
Don't do any advanced stuff out of the box
Keep foreign keys in tables pure, no ->cascase etc stuff

## Commits

Never commit without the explicit agreement of the user. Do not commit automatically at the end of a task, and do not assume that a finished change should be committed. Always ask first and wait for a clear yes.

Use a single line commit message in this format:

```
TYPE :emoji: Description
```

- Write the type in uppercase, followed by a space, a colon, a space, the emoji, a space, and the description.
- Do not add a body, bullet points, or a "Co-authored-by" trailer.
- Write the description in sentence case, starting with a capital letter and describing the change in the present tense.
- Keep the description short and specific.

Types and emoji used in this project:

| Type | Emoji | Use for |
| --- | --- | --- |
| INIT | :seedling: | The initial commit |
| FEATURE | :sparkles: | New functionality |
| BUGFIX | :bug: | Fixing incorrect behavior |
| REFACTOR | :hammer: | Restructuring code without changing behavior |
| DESIGN | :art: | Styling and layout changes |
| CONTENT | :pencil: | Content and documentation changes |

Examples from this project:

```
FEATURE :sparkles: Add CRUD for Article
BUGFIX :bug: Fix naming mistake
REFACTOR :hammer: Switch to resourceful routes
DESIGN :art: Update the app layout
CONTENT :pencil: Add project specific rules
```

Only stage files that belong to the change being committed. Leave unrelated modified or untracked files out of the commit.
