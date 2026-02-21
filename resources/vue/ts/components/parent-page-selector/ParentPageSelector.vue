<template>
    <div class="row p-3">
        <div class='form-group col-xs-12 col-md-6'>
            <label for='level'>{{ label }}</label>
            <select class='form-select' name='parent_select' id='level' v-model="submenuOpened">
                <option value='0'>Main menu</option>
                <option value='1'>Submenu</option>
            </select>
        </div>
        <div class='form-group col-xs-12 col-md-6' id='submenus' v-show="submenuOpened==1">
            <label for='submenus'>Parent menu:</label>
            <select class='form-select' name='parent_id'>
                <optgroup   v-for="(label, code) in languages" :key="code" :label="label">
                    <option v-for="each in pagesByLanguage(code)" :key="each.id" :value="each.id" :selected="page && each.id === page.parent_id">
                            {{ each.name }}
                    </option>
                </optgroup>
            </select>
        </div>
    </div>
</template>

<script lang="ts" src="./ParentPageSelector.ts" />