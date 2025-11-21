# 🚀 WordPress Deployment Guide V3

### _(Zero Downtime · Safe Diff · Auto-Cleanup · Auto-Release Notes · Incremental Deploys)_

This guide documents the complete automated deployment system for your WordPress plugins and themes.  
Just push to the `main` branch — and the entire workflow triggers automatically:

- Zero-downtime deployments
- Auto-versioned releases
- Deploy only changed plugins/themes
- Auto-cleanup of old releases (keeps last 5)
- Auto-generate GitHub Releases + release notes
- Rollback-ready symlink architecture

---

# 📁 1. Repository Structure

Your WordPress project lives inside a monorepo:

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

Each folder in:

- `plugins/*` → treated as a WordPress plugin
- `themes/*` → treated as a WordPress theme

Only folders that **actually changed** in the commit history will be deployed.

---

# 🔐 2. Required GitHub Repository Secrets

Go to:

```
Repository → Settings → Secrets and variables → Actions
```

Create these secrets:

| Secret Name | Example            | Purpose                     |
| ----------- | ------------------ | --------------------------- |
| `SSH_HOST`  | example.com        | Hostinger hostname          |
| `SSH_PORT`  | 65002              | Hostinger SSH port          |
| `SSH_USER`  | u12345678          | SSH username                |
| `SSH_KEY`   | (your private key) | For GitHub Actions SSH auth |
| `WP_PATH`   | public_html        | WordPress install directory |

---

# 🔑 3. SSH Deployment Key Setup

You will generate:

### ✔ A **private key** → stored in GitHub (`SSH_KEY`)

### ✔ A **public key** → stored in Hostinger

## Step 1: Generate SSH Key Pair

Run in Terminal:

```bash
ssh-keygen -t ed25519 -C "wp-deploy-key"
```

When asked where to save:

```
/Users/you/.ssh/wp_deploy_hostinger
```

(Use a _new_ name. Never overwrite existing keys.)

When asked for a passphrase → press Enter twice (empty).

This creates:

```
~/.ssh/wp_deploy_hostinger        ← PRIVATE KEY
~/.ssh/wp_deploy_hostinger.pub    ← PUBLIC KEY
```

---

## Step 2: Add PUBLIC Key to Hostinger

In **hPanel**:

```
Advanced → SSH Access → Add SSH Key
```

Paste the content of:

```
~/.ssh/wp_deploy_hostinger.pub
```

Save.

---

## Step 3: Add PRIVATE Key to GitHub (`SSH_KEY`)

Run:

```bash
cat ~/.ssh/wp_deploy_hostinger
```

Copy everything:

```
-----BEGIN OPENSSH PRIVATE KEY-----
...
-----END OPENSSH PRIVATE KEY-----
```

In GitHub:

```
SSH_KEY
```

Paste the entire private key and save.

---

# ⚙️ 4. How the Deployment Workflow Works

Your workflow performs:

1. Fetch full git history (`fetch-depth: 0`)
2. Use **safe diff**:
   ```
   git diff --name-only origin/main HEAD
   ```
   → identifies all changed plugins/themes
3. Auto-versioning (timestamp release)
4. Only changed items are zipped and uploaded
5. Zero-downtime extraction via:
   ```
   releases/<version>/
   current → releases/<version>
   ```
6. Auto-activation of the plugin/theme with WP-CLI
7. Cleanup:
   - Keeps latest 5 releases
   - Removes older ones
8. Auto-create GitHub Release + release notes

This ensures:

- reliable
- incremental
- rollback-safe
- fast
- fully automated deployments

---

# 🔎 5. Workflow Requirement: Full Git History

Because safe diff compares:

```
origin/main → HEAD
```

the workflow **must** enable:

```yaml
with:
  fetch-depth: 0
```

This is already included.

---

# 📦 6. Server Folder Structure (After Deployment)

Example plugin:

```
wp-content/plugins/plugin-one/
  releases/
    20250101-101500/
    20250105-081201/
    20250107-143322/
  current → releases/20250107-143322
```

Example theme:

```
wp-content/themes/theme-one/
  releases/
    20250101-101500/
    20250105-081201/
    20250107-143322/
  current → releases/20250107-143322
```

The live version is always loaded from:

```
current/
```

---

# 🔁 7. Rollback Instructions

Rollback is instant. No downtime.

## Rollback a plugin:

```bash
cd wp-content/plugins/plugin-one
ls releases
ln -sfn releases/<old-version> current
```

## Rollback a theme:

```bash
cd wp-content/themes/theme-one
ls releases
ln -sfn releases/<old-version> current
```

---

# 🧹 8. Automatic Release Cleanup (Keep Last 5)

To prevent server bloat:

- The workflow keeps the **5 newest releases**
- Deletes all older releases

This happens automatically during deployment.

---

# 📝 9. Auto-Generated GitHub Releases

Each deployment creates a GitHub Release containing:

### ✔ version tag

### ✔ summary of changed files

### ✔ plugins/themes that were deployed

### ✔ auto-generated release notes

This provides a full deployment audit trail.

---

# 🚨 10. Troubleshooting

### SSH Failure

- Ensure public key exists in Hostinger
- Ensure private key is correct in GitHub
- Ensure SSH port is correct

### Nothing deployed?

This means **no plugin/theme files changed** since the last main commit.

### Incorrect WordPress path?

Check your WordPress installation folder and set:

```
WP_PATH = public_html
```

---

# 🎉 11. Deployment Summary

To deploy:

1. Make changes to your plugin or theme
2. Commit and push to `main`

GitHub Actions will:

- detect changes
- build ZIPs
- upload to server
- deploy with zero downtime
- activate plugins/themes
- clean old releases
- create GitHub release notes

No FTP.  
No manual uploads.  
Fully automated.  
Production safe.
