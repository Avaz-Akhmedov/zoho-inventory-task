<template>
    <div class="inventory-container">
        <h1>Inventory Items</h1>

        <ul class="items-list">
            <li v-for="item in inventoryItems" :key="item.item_id" class="item-card">
                <div class="item-info">
                    <h3>{{ item.name }}</h3>
                    <p>Price: ${{ item.rate }}</p>
                    <p>Available Stock: {{ item.actual_available_stock }}</p>
                </div>
                <div class="quantity-controls">
                    <button @click="decreaseQuantity(item.item_id)">-</button>
                    <span>{{ quantities[item.item_id] || 0 }}</span>
                    <button @click="increaseQuantity(item.item_id, item.actual_available_stock)">+</button>
                </div>

                <button @click="cartHandler(item)" class="card-btn">Add to cart</button>
            </li>
        </ul>
    </div>
</template>

<script setup>
import {ref, onMounted} from 'vue'
import axios from 'axios'
import {addToCart} from '../Stores/сart.js'


const inventoryItems = ref([])
const error = ref(null)
const quantities = ref({})

const fetchInventoryItems = async () => {
    try {
        const response = await axios.get('/api/zoho/inventory-items')
        inventoryItems.value = response.data
    } catch (err) {
        error.value = 'Failed to fetch inventory items.'
    }
}

const increaseQuantity = (itemId, maxStock) => {
    const current = quantities.value[itemId] || 0
    if (current < maxStock) {
        quantities.value[itemId] = current + 1

    }
}

const decreaseQuantity = (itemId) => {
    const current = quantities.value[itemId] || 0
    if (current > 0) {
        quantities.value[itemId] = current - 1
    }
}

const cartHandler = (item) => {
    const itemQuantities = quantities.value[item.item_id] || 0
    const success = addToCart(item,itemQuantities);

    if (success) {
        alert('✅ Successfully added to cart!')
        quantities.value[item.item_id] = 0;
    } else {
        alert('⚠️ Please select a quantity greater than 0.')
    }
}


onMounted(() => {
    fetchInventoryItems()
})
</script>
