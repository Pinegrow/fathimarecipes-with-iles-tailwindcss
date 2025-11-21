# 🚀 WordPress Deployment Guide V4

### _(Zero Downtime · Safe Diff (HEAD~1) · Auto-Cleanup · Auto-Release Notes · Incremental Deploys)_

This guide documents the **final, corrected, production-ready** deployment system for your WordPress plugins and themes.

It includes the **correct safe-diff method** for GitHub Actions:

```
git diff --name-only HEAD~1 HEAD
```

This always detects file changes correctly and avoids the “empty diff” issue that occurred earlier.

---

# 📁 1. Repository Structure

Your WordPress project lives inside:

```
vuedesigner/
  pg-wordpress/
    plugins/
      plugin-one/
      plugin-two/
      ...
    themes/
      theme-one/
      theme-two/
      ...
```

Each folder inside:

- `plugins/*` → treated as a single WordPress plugin
- `themes/*` → treated as a single WordPress theme

Only changed folders are deployed.

---

# 🔐 2. Required GitHub Repository Secrets

Go to:

```
Repository → Settings → Secrets and variables → Actions
```

Create:

| Secret Name | Example       | Purpose                        |
| ----------- | ------------- | ------------------------------ |
| SSH_HOST    | example.com   | Hostinger hostname             |
| SSH_PORT    | 65002         | Hostinger SSH port             |
| SSH_USER    | u12345678     | SSH username                   |
| SSH_KEY     | (private key) | Private SSH key for deployment |
| WP_PATH     | public_html   | WordPress install directory    |

---

# 🔑 3. SSH Deployment Key Setup

Generate key pair:

```bash
ssh-keygen -t ed25519 -C "wp-deploy-key"
```

Save to:

```
/Users/you/.ssh/wp_deploy_hostinger
```

No passphrase.

This produces:

- `wp_deploy_hostinger` → **private key** (add to GitHub `SSH_KEY`)
- `wp_deploy_hostinger.pub` → **public key** (upload to Hostinger)

---

# ⚙️ 4. How the Deployment Works

### ✔ Uses safe diff: `HEAD~1 → HEAD`

### ✔ Deploys only changed plugins/themes

### ✔ Zero downtime via `releases/<version>` + `current` symlink

### ✔ Keeps last 5 releases

### ✔ Auto GitHub Release with changelog

---

# 🔎 5. Safe Diff Requirement

GitHub checkout must fetch at least 2 commits:

```yaml
with:
  fetch-depth: 2
```

This avoids empty diffs.

---

# 📦 6. Server Folder Structure

```
wp-content/plugins/my-plugin/
  releases/
    20250101-101500/
    20250105-081201/
    20250107-143322/
  current → releases/20250107-143322
```

Same for themes.

---

# 🔁 7. Rollback Procedure

Rollback instantly:

```bash
cd wp-content/plugins/plugin-one
ln -sfn releases/<older-version> current
```

Or:

```bash
cd wp-content/themes/theme-one
ln -sfn releases/<older-version> current
```

---

# 🧹 8. Auto Cleanup

Deployment keeps **last 5 releases**:

```
ls -dt releases/* | tail -n +6 | xargs rm -rf
```

This prevents server bloat.

---

# 📝 9. Auto GitHub Releases

Each deployment creates:

- A GitHub Release
- With version tag
- Listing changed files
- Full release notes

---

# 🚨 10. Troubleshooting

### Nothing deployed?

No plugin/theme files changed between HEAD~1 and HEAD.

### SSH login fails?

Check:

- Hostinger SSH Access
- SSH key
- Port
- Username

### Wrong WordPress path?

Set:

```
WP_PATH = public_html
```

---

# 🎉 11. Deployment Summary

To deploy:

1. Commit changes
2. Push to `main`

GitHub Actions:

- detects changed folders
- builds ZIPs
- uploads via SCP
- extracts to releases
- updates symlinks
- activates plugins/themes
- cleans old releases
- creates release notes

Fully automated.  
Production safe.  
Zero downtime.
