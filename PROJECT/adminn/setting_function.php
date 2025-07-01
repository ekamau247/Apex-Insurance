<?php
// Get a setting by key
function get_setting($con, $key) {
    $stmt = $con->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $stmt->close();
        return null; // Or use '' if you prefer
    }

    $stmt->bind_result($value);
    $stmt->fetch();
    $stmt->close();
    return $value;
}

// Set (update) a setting by key
function set_setting($con, $key, $value) {
    $stmt = $con->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
    $stmt->bind_param("ss", $value, $key);
    $stmt->execute();
    $stmt->close();
}

// Insert or update a setting
function create_or_update_setting($con, $key, $value) {
    $stmt = $con->prepare("INSERT INTO system_settings (setting_key, setting_value)
                           VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    $stmt->bind_param("ss", $key, $value);
    $stmt->execute();
    $stmt->close();
}

// Delete a setting
function delete_setting($con, $key) {
    $stmt = $con->prepare("DELETE FROM system_settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $stmt->close();
}

// Get all settings as key => value pairs
function get_all_settings($con) {
    $settings = [];
    $result = $con->query("SELECT setting_key, setting_value FROM system_settings");
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}
?>
