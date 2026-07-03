#!/bin/bash
set -e
cd /Users/laricelk/Documents/ProximaJob-livrable
git stash drop 2>/dev/null || true
git checkout -b fix/cv-flow-deepseek-iframe
git add -A
git commit -m "fix: CV flow - missing methods, DeepSeek analysis, iframe zoom, security fixes

- Added missing controller methods: inlinePrincipalPdf, downloadPrincipalPdf, inlineCV, uploadSourceCv, importFromUploadedCv
- CV analysis with DeepSeek (priority) + Gemini fallback + regex fallback
- Iframe zoom at 87% for PDF preview
- CSP middleware implemented
- Rate limiting on /verify-password
- Removed 11 exposed debug routes from web.php
- FK constraints migration + SoftDeletes + performance indexes
- Fixed candidate bugs: route params, null safety, duplicate code removed
- Updated candidate demo test to cover complete journey

Co-Authored-By: Claude <noreply@anthropic.com>"
git push origin fix/cv-flow-deepseek-iframe
echo "✅ Pushed to origin/fix/cv-flow-deepseek-iframe"