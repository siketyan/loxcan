# loxcan
![PHP](https://github.com/siketyan/loxcan/workflows/PHP/badge.svg)
![Action](https://github.com/siketyan/loxcan/workflows/Action/badge.svg)

Universal Lock File Scanner for Git.

## 🚀 Motivation
Today, most languages have a package manager, and some language have two.
Dependency management is very important and difficult in software development.

In cases of code review, we check entire of the changed codes.
However, we often ignore lock files in the review, which controls dependencies of the project or the library.

On GitHub Pull Request, most lock files are hidden by default.

![Load diff screen](./resources/load-diff.png)

Actually, they are very long and not human-readable.

I tried to notify the diff of the lock files to the author of PR and/or the reviewer(s).
Using this action, the added, upgraded, downgraded, and removed packages are reported to the PR, in user-friendly format.

![Report of the changed packages](./resources/screenshot.png)

So we can check what packages will be changed by the PR, in the review.

## ✨ Installation
### In GitHub Actions
Use `pull_request` events to trigger the action.

```yaml
steps:
  - uses: actions/checkout@v2
    with:
      fetch-depth: 0

  - uses: siketyan/loxcan@v0.1
    with:
      owner: ${{ github.event.repository.owner.login }}
      repo: ${{ github.event.repository.name }}
      base: ${{ github.event.pull_request.base.ref }}
      head: ${{ github.event.pull_request.head.ref }}
      issue_number: ${{ github.event.pull_request.number }}
      token: ${{ github.token }}
```

## 📦 Supported Package Managers
(✅ = Supported, ⬜️ = Scheduled)

- [x] Composer (PHP)
- [ ] Cargo (Rust)
- [x] npm (JavaScript, Node.js)
- [ ] Yarn (JavaScript, Node.js)

## 📋 Supported Reporters
(✅ = Supported, ⬜️ = Scheduled)

- [x] GitHub
- [ ] GitLab
