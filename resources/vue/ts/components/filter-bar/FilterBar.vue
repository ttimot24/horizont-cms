<template>
    <div>
        <form class="d-flex gap-2 mb-3 w-75" method="GET" action="">
            <div class="input-group">
                  <!-- Dropdown -->
                <select class="form-select" name="filter_field" id="filterFieldSelect" v-model="selectedField">
                    <option v-for="option in filterOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                </select>
              
                    <!-- Hidden actual filter input (dynamically named) -->
                <input type="hidden" :name="actualName" :value="queryValue" id="actualFilterInput">

                  <!-- Search input -->
                <input type="text" class="form-control" name="query" id="searchInput" v-model="queryValue" :placeholder="$t('actions.search') + '...'">
            </div>
              
                <!-- Submit button -->
            <button class="btn btn-primary" type="submit">{{ $t('actions.search') }}</button>
        </form>
    </div>
</template>

<script lang="ts" src="./FilterBar.ts"/>