<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditLogService
{
    /**
     * Create an audit log entry with clean and reusable interface.
     * Automatically JSON encodes old_data and new_data.
     *
     * @param string $action The action type (CREATE, UPDATE, DELETE, etc.)
     * @param string $tableName The table name where action was performed
     * @param string $description User-friendly description of the action
     * @param int|null $recordId The ID of the affected record (optional)
     * @param array|null $oldData Previous data for updates (optional)
     * @param array|null $newData New data for creates/updates (optional)
     * @return AuditLog
     */
    public static function log(
        string $action,
        string $tableName,
        string $description,
        ?int $recordId = null,
        ?array $oldData = null,
        ?array $newData = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'description' => $description,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
        ]);
    }

    /**
     * Log a CREATE action.
     *
     * @param string $tableName
     * @param string $description
     * @param int $recordId
     * @param array|null $newData
     * @return AuditLog
     */
    public static function create(
        string $tableName,
        string $description,
        int $recordId,
        ?array $newData = null
    ): AuditLog {
        return self::log('CREATE', $tableName, $description, $recordId, null, $newData);
    }

    /**
     * Log an UPDATE action.
     *
     * @param string $tableName
     * @param string $description
     * @param int $recordId
     * @param array|null $oldData
     * @param array|null $newData
     * @return AuditLog
     */
    public static function update(
        string $tableName,
        string $description,
        int $recordId,
        ?array $oldData = null,
        ?array $newData = null
    ): AuditLog {
        return self::log('UPDATE', $tableName, $description, $recordId, $oldData, $newData);
    }

    /**
     * Log a DELETE action.
     *
     * @param string $tableName
     * @param string $description
     * @param int $recordId
     * @param array|null $oldData
     * @return AuditLog
     */
    public static function delete(
        string $tableName,
        string $description,
        int $recordId,
        ?array $oldData = null
    ): AuditLog {
        return self::log('DELETE', $tableName, $description, $recordId, $oldData);
    }
}
