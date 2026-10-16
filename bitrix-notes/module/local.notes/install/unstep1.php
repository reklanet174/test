<?php

use Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid()) {
    return;
}

Loc::loadMessages(__FILE__);
?>
<form action="<?= $APPLICATION->GetCurPage() ?>">
    <?= bitrix_sessid_post() ?>
    <input type="hidden" name="lang" value="<?= LANG ?>">
    <input type="hidden" name="id" value="local.notes">
    <input type="hidden" name="uninstall" value="Y">
    <input type="hidden" name="step" value="2">

    <p><?= Loc::getMessage('LOCAL_NOTES_UNINSTALL_WARN') ?: 'Будут удалены REST, urlrewrite и регистрация модуля.' ?></p>

    <p>
        <input type="checkbox" name="savedata" id="savedata" value="Y">
        <label for="savedata">
            <?= Loc::getMessage('LOCAL_NOTES_SAVE_DATA') ?: 'Сохранить таблицу b_local_notes (не удалять данные)' ?>
        </label>
    </p>

    <input type="submit" name="inst" value="<?= Loc::getMessage('MOD_UNINST_DEL') ?: 'Удалить модуль' ?>">
</form>
