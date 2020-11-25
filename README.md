# loxcan
![PHP](https://github.com/siketyan/loxcan/workflows/PHP/badge.svg)
![Action](https://github.com/siketyan/loxcan/workflows/Action/badge.svg)

Universal Lock File Scanner for Git.

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
- [ ] npm (JavaScript, Node.js)
- [ ] Yarn (JavaScript, Node.js)

## 📋 Supported Reporters
(✅ = Supported, ⬜️ = Scheduled)

- [x] GitHub
- [ ] GitLab
