---
description: Rollback Procedures for Deployments
---
# Emergency Rollback Guide

## Scenario 1: Revert to Last Safe Point (VPS)

If the recent deployment caused critical errors, follow these steps to revert the code to the safety tag created before the push.

1.  **Access the VPS SSH:**
    ```bash
    ssh user@your-vps-ip
    cd /path/to/your/project
    ```

2.  **Fetch the Safety Tag:**
    Ensure you have the latest tags from the repository.
    ```bash
    git fetch --tags
    ```

3.  **Checkout the Safety Tag:**
    This will detach the HEAD and place the code exactly as it was before the dashboard/billing update.
    ```bash
    git checkout safety-rollback-2026-02-10
    ```

4.  **Verify Stability:**
    Check if the application is running correctly (php artisan serve or check the live site).

5.  **Revert Main Branch (Optional - if you want to stay reverted):**
    If the changes are permanently broken and you need `main` to reflect the old state:
    ```bash
    git checkout main
    git reset --hard safety-rollback-2026-02-10
    git push -f origin main
    ```
    *Warning: Force pushing rewrites history. Use with caution.*

## Scenario 2: Quick Hotfix (Local)

If the issue is minor (e.g., a typo or color fix), it is better to fix it locally and push a new commit rather than rolling back.

1.  Fix the issue in your local environment.
2.  Commit and push:
    ```bash
    git add .
    git commit -m "fix: critical issue with [feature]"
    git push origin main
    ```
3.  Pull on VPS:
    ```bash
    git pull origin main
    ```
