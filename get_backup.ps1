# ==== CONFIG ====
$VPS_USER = "root"                       # user SSH VPS
$VPS_IP = "157.66.24.104"                 # IP VPS
$VPS_BACKUP_DIR = "/home/backup"          # thư mục backup trên VPS
$LOCAL_SAVE_DIR = "D:\BackupVPS"          # nơi lưu trên Windows

# ==== GỌI BACKUP TRÊN VPS ====
Write-Host "🚀 Đang chạy backup trên VPS..."
ssh "${VPS_USER}@${VPS_IP}" "bash ${VPS_BACKUP_DIR}/backup.sh"

# ==== LẤY FILE MỚI NHẤT ====
Write-Host "📥 Đang tìm file backup mới nhất..."
$latestFile = ssh "${VPS_USER}@${VPS_IP}" "ls -t ${VPS_BACKUP_DIR}/full_backup_*.zip | head -n 1"

# ==== TẢI VỀ ====
Write-Host "📂 Đang tải về file: $latestFile"
scp "${VPS_USER}@${VPS_IP}:$latestFile" "$LOCAL_SAVE_DIR"

Write-Host "✅ Hoàn tất! File đã lưu ở: $LOCAL_SAVE_DIR"
