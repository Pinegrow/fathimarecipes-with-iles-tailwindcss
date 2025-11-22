# 🚀 WordPress Deployment Guide V7

### _(Zero Downtime · Safe Diff (HEAD~1) · Auto-Cleanup · Auto-Release Notes · Incremental Deploys)_

This guide documents the deployment system for your WordPress plugins and themes — aligned with a real Hostinger environment, but should be similar for other hostings.

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

- Every folder under `plugins/*` is treated as an independent WordPress plugin.
- Every folder under `themes/*` is treated as an independent WordPress theme.
- **Only changed folders are deployed.**

---

# 🔐 2. Required GitHub Repository Secrets

Go to:

```
Repository → Settings → Secrets and variables → Actions
```

Create these:

| Secret Name | Example                                      | Purpose                            |
| ----------- | -------------------------------------------- | ---------------------------------- |
| `SSH_HOST`  | 145.79.28.64                                 | Hostinger SSH hostname/IP          |
| `SSH_PORT`  | 65002                                        | Hostinger SSH port                 |
| `SSH_USER`  | u865414922                                   | Your Hostinger SSH user            |
| `SSH_KEY`   | _your private key_                           | SSH private key for GitHub Actions |
| `WP_PATH`   | domains/admin.fathimarecipes.com/public_html | WordPress install directory        |

✔ **IMPORTANT:**  
`WP_PATH` gets combined inside workflow into:

```
/home/${SSH_USER}/${WP_PATH}/wp-content
```

Which accurately becomes:

```
/home/u865414922/domains/admin.fathimarecipes.com/public_html/wp-content
```

---

# 🔑 3. SSH Deployment Key Setup

### 1️⃣ Generate a new SSH key (no passphrase)

```bash
ssh-keygen -t ed25519 -C "wp-deploy-key"
```

Save to:

```
~/.ssh/id_ed25519
```

This creates:

- `id_ed25519` → **private key** (add to GitHub Secret: `SSH_KEY`)
- `id_ed25519.pub` → **public key** (upload to Hostinger)

### 2️⃣ Add public key to Hostinger

Hostinger → **SSH Access → Add SSH Key**

Paste the contents of:

```
~/.ssh/id_ed25519.pub
```

### 3️⃣ Add private key to GitHub → Secret `SSH_KEY`

Paste the entire:

```
-----BEGIN OPENSSH PRIVATE KEY-----
...
-----END OPENSSH PRIVATE KEY-----
```

No extra spaces, no line breaks altered.

---

# ⚙️ 4. Deployment Flow Explained

Your deployment workflow v7 performs:

1. **Safe diff** between commits
   ```
   git diff --name-only HEAD~1 HEAD
   ```
2. Detects changed plugins/themes only.
3. Creates ZIPs from repo root (path-safe).
4. Uploads ZIPs to Hostinger via SCP.
5. Extracts into versioned folders:
   ```
   wp-content/plugins/<plugin>/releases/<timestamp>
   ```
6. Updates `current` symlink → ZERO downtime.
7. Cleans old releases (keeps latest 5).
8. Activates the plugin or theme via WP-CLI.
9. Creates GitHub Release with changelog.

---

# 🔎 5. Safe Diff Requirement

Your workflow must fetch at least 2 commits:

```yaml
fetch-depth: 2
```

This ensures `HEAD~1 → HEAD` always works and avoids empty diffs.

---

# 📦 6. Hostinger Server Folder Structure After Deployment

Plugins:

```
/home/u865414922/domains/admin.fathimarecipes.com/public_html/wp-content/plugins/<plugin>/
  releases/
    20250101-101500/
    20250105-081201/
    20250107-143322/
  current → releases/20250107-143322
```

Themes:

```
/home/u865414922/domains/admin.fathimarecipes.com/public_html/wp-content/themes/<theme>/
  releases/
    20250101-101500/
    20250105-081201/
    20250107-143322/
  current → releases/20250107-143322
```

---

# 🔁 7. Rollback Procedure

Instant, no downtime:

## Plugin rollback:

```bash
cd /home/u865414922/domains/admin.fathimarecipes.com/public_html/wp-content/plugins/<plugin>/
ln -sfn releases/<old-version> current
```

## Theme rollback:

```bash
cd /home/u865414922/domains/admin.fathimarecipes.com/public_html/wp-content/themes/<theme>/
ln -sfn releases/<old-version> current
```

Done.

---

# 🧹 8. Automatic Cleanup (Keep Last 5 Releases)

Each deploy automatically removes old versions:

```
ls -dt releases/* | tail -n +6 | xargs rm -rf
```

This prevents storage bloat on Hostinger.

---

# 📝 9. Automatic GitHub Releases

Each deployment generates a Release with:

- Version tag (timestamp)
- List of changed files
- Execution summary
- Deployment logs in Actions tab

---

# 🚨 10. Troubleshooting

### ❗ Deployment skipped

No plugin/theme changes in latest commit.

### ❗ SSH authentication failing

Likely reasons:

- Public key not added to Hostinger
- Private key incorrectly formatted in GitHub Secret
- Wrong SSH port

### ❗ Theme not appearing in `public_html/wp-content/themes`

Your real WP directory is at:

```
/home/u865414922/domains/admin.fathimarecipes.com/public_html
```

NOT `/public_html/wp-content`.

Ensure `WP_PATH` secret matches exactly.

---

# 🎉 11. Deployment Summary

To deploy:

1. Modify your plugin/theme files
2. Commit
3. Push to `main`

GitHub Actions will:

- Detect changed plugin/theme
- Build ZIPs
- Upload to Hostinger
- Extract into new release folder
- Switch symlink (zero downtime)
- Clean old releases
- Activate plugin/theme
- Create GitHub Release

This system is:

- ✔ Production-safe
- ✔ Zero downtime
- ✔ Reversible
- ✔ Fully automated
