<?php

use Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid()) {
    return;
}

echo CAdminMessage::ShowNote(
    Loc::getMessage('LOCAL_NOTES_UNINSTALL_OK') ?: 'Модуль local.notes удален'
);
?>
<form action="<?= $APPLICATION->GetCurPage() ?>">
    <input type="hidden" name="lang" value="<?= LANG ?>">
    <input type="submit" name="" value="OK">
</form>
