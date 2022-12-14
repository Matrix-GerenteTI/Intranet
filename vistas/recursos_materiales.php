<!-- component template -->
<div id="tableContent">
    <script type="text/x-template" id="grid-template">
        <table id="recursos">
            <thead>
                <tr>
                    <th id="thTable" v-for="key in columns"
                        @click="sortBy(key)"
                        :class="{ active: sortKey == key }">
                        {{ key | capitalize }}
                        <span class="arrow" :class="sortOrders[key] > 0 ? 'asc' : 'dsc'">
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="entry in filteredData">
                    <td id="tdTable" v-for="key in columns">
                        {{entry[key]}}
                    </td>
                </tr>
            </tbody>
        </table>
    </script>
    <!-- demo root element -->
    <div id="demo">
        <form id="search">
            Search <input name="query" v-model="searchQuery">
        </form>
        <demo-grid
            :data="gridData"
            :columns="gridColumns"
            :filter-key="searchQuery">
        </demo-grid>
    </div>
</div>