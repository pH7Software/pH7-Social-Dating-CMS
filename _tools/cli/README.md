# 🚀 pH7Builder CLI

## Installation guidance

- [Good to Know](#-good-to-know)
- [How pH7Builder CLI installation can be useful](#-how-ph7builder-cli-installation-can-be-useful)
- [Troubleshooting](#-troubleshooting)
- [How this library was built. Backstage)](#-how-i-built-this-backstage-full-video)
- [Contact. Say Hi](#-contact--say-hello)

The legacy interactive CLI installer is disabled because it could leave a partial installation.
Use the supported browser installer documented in the root [README](../../README.md#quick-start).
Running `php ph7cms setup:install` prints that guidance without changing the site or database.

For GitHub issue maintenance, you can also run:

`php ph7cms github:issues:resolve 1202 1201 --dry-run`

Useful options:

- `--comment-file=/path/to/comment.md` to post the same comment on every issue
- `--comment-dir=/path/to/comments` to load one Markdown file per issue number, such as `1202.md`
- `--close` to close the issues after commenting
- `--dry-run` to inspect the planned actions without modifying GitHub

The command uses `GITHUB_TOKEN` or `GH_TOKEN` from the environment for authenticated comment and close operations.
Prefer environment variables over `--token` so the secret does not end up in shell history.


## 🤕 Troubleshooting

If you encounter an issue, please report
them [by raising an issue on GitHub](https://github.com/pH7Software/pH7-Social-Dating-CMS/issues).


## 🎥 How I built this? Backstage. Full video

[![Watch the video](https://i1.ytimg.com/vi/qFJrezJ2X8s/sddefault.jpg)](https://www.youtube.com/watch?v=qFJrezJ2X8s)

👉 **[Click here to watch on YouTube](https://www.youtube.com/watch?v=qFJrezJ2X8s)**


## Creator and project

pH7Builder was created by [Pierre-Henry Soria](https://ph7.me)
([GitHub](https://github.com/pH-7)) and is maintained in the
[pH7Software organization](https://github.com/pH7Software).
