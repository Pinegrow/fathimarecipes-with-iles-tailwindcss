# 🚀 WordPress Deployment Guide V8

### _(Flat SCP Upload Paths • Zero Downtime • Safe Diff (HEAD~1) • Auto Cleanup • Auto Release Notes • Hostinger-Compatible)_

This guide describes the **auto-deployment system** for your WordPress plugins & themes on **Hostinger**, matching your real environment. This system can easily be adopted for other hosting services that provides SSH access.

---

# 📁 1. Repository Structure

```
vuedesigner/
  pg-wordpress/
    plugins/
      plugin-one/
      plugin-two/
    themes/
      theme-one/
      theme-two/
```

- Every folder under `plugins/*` is treated as a separate plugin
- Every folder under `themes/*` is treated as a separate theme
- **Only changed items are deployed**

---

# 🔐 2. Required GitHub Repository Secrets

Go to:

```
Repository → Settings → Secrets and Variables → Actions
```

Create:

| Secret   | Example                                      | Purpose                         |
| -------- | -------------------------------------------- | ------------------------------- |
| SSH_HOST | 145.79.28.64                                 | Hostinger SSH hostname/IP       |
| SSH_PORT | 65002                                        | SSH port                        |
| SSH_USER | u865414922                                   | Hostinger system user           |
| SSH_KEY  | _private key_                                | SSH private key (NO passphrase) |
| WP_PATH  | domains/admin.fathimarecipes.com/public_html | WordPress install path          |

The workflow constructs:

```
/home/${SSH_USER}/${WP_PATH}/wp-content
```

Which becomes:

```
/home/u865414922/domains/admin.fathimarecipes.com/public_html/wp-content
```

✔ EXACT real WordPress path for your Hostinger installation.

---

# 🔑 3. SSH Key Setup (Hostinger → GitHub)

### 1️⃣ Generate SSH key

(No passphrase)

```bash
ssh-keygen -t ed25519 -C "wp-deploy"
```

This creates:

- `~/.ssh/id_ed25519` → **PRIVATE KEY**
- `~/.ssh/id_ed25519.pub` → **PUBLIC KEY**

### 2️⃣ Add public key to Hostinger

Hostinger → **SSH Access → Add SSH Key**

Paste:

```
cat ~/.ssh/id_ed25519.pub
```

### 3️⃣ Add private key to GitHub secret `SSH_KEY`

Paste full contents:

```
-----BEGIN OPENSSH PRIVATE KEY-----
...
-----END OPENSSH PRIVATE KEY-----
```

⚠ Do NOT reformat or remove line breaks.

---

# ⚙️ 4. Deployment Flow (Workflow v8)

Workflow v8 performs:

1. Safe diff: `git diff --name-only HEAD~1 HEAD`
2. Detect plugin/theme folder changes
3. Build ZIPs from repo root
4. Upload ZIPs into **flat Hostinger folders**:

```
/deploy/deploy_build/plugins/*.zip
/deploy/deploy_build/themes/*.zip
```

5. Deploy step extracts them into:

```
wp-content/plugins/<plugin>/releases/<timestamp>
wp-content/themes/<theme>/releases/<timestamp>
```

6. Updates symlink:

```
current -> releases/<timestamp>
```

7. Activates plugin/theme via WP-CLI
8. Cleans old releases (keep last 5)
9. Creates GitHub Release

---

# 📦 5. Hostinger Server Folder Structure After Deployment

### Plugins

```
/home/u865414922/domains/admin.fathimarecipes.com/public_html/wp-content/plugins/<plugin>/
  releases/
    20250101-101500/
    20250105-222210/
    20250107-130101/
  current → releases/20250107-130101
```

### Themes

```
/home/u865414922/domains/admin.fathimarecipes.com/public_html/wp-content/themes/<theme>/
  releases/
    20250101-101500/
    20250105-222210/
    20250107-130101/
  current → releases/20250107-130101
```

---

# 🔁 6. Rollback (Instant, Zero-Downtime)

If a deployment fails or you need to revert:

### Plugin rollback

```bash
cd wp-content/plugins/<plugin>
ln -sfn releases/<older_timestamp> current
```

### Theme rollback

```bash
cd wp-content/themes/<theme>
ln -sfn releases/<older_timestamp> current
```

Done.

---

# 🧹 7. Automatic Cleanup (Keep Last 5 Releases)

Workflow removes older releases automatically:

```
ls -dt releases/* | tail -n +6 | xargs rm -rf
```

This prevents Hostinger storage issues.

---

# 📝 8. Automatic GitHub Releases

Each deployment publishes:

- A new GitHub Release
- With auto-generated release notes
- Timestamp version tag
- List of changed files used for deployment

---

# 🚨 9. Troubleshooting

### ❗ “ZIP uploaded but nothing deployed”

Cause before v8: ZIPs uploaded into wrong nested folder.  
V8 fixes this using **flat deploy folder**.

### ❗ “SSH authentication failed”

Most common:

- Private key misformatted in GitHub Secrets
- Public key missing in Hostinger
- Wrong SSH port (Hostinger rotates)

### ❗ “Theme not appearing”

Ensure `WP_PATH` secret EXACTLY matches:

```
domains/admin.fathimarecipes.com/public_html
```

---

# 🚀 10. How to Deploy (Simple Workflow)

1. Modify plugin/theme
2. Commit
3. Push → `main`

GitHub Actions will:

- Detect diffs
- Build ZIPs
- Upload
- Deploy
- Activate
- Create symlink
- Cleanup
- Release

Fully automated.

---

# 🎉 11. Summary

Your Workflow v8 + this Deployment Guide V8 ensure:

✔ Fully automated CI/CD deployment  
✔ Zero downtime  
✔ Correct WordPress directory  
✔ Flat SCP upload paths  
✔ Correct extraction + symlink switching  
✔ Auto cleanup  
✔ Auto GitHub Releases  
✔ Accurate safe diff logic  
✔ Hostinger-compatible
