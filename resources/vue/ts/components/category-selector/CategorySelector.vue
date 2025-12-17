<template>
    <div class='form-group col-12 mb-4'>
        <label v-if="label" for='sel1'>{{ label }}</label>
        <select class='form-select' name='category_ids[]' id='category_select' multiple required>

            <option v-for="(category) in categories" :value="category.id"
                :selected="selected_categories.includes(category.id)">
                {{ category.name }}
            </option>

        </select>
    </div>
</template>

<script lang="ts" src="./CategorySelector.ts" />