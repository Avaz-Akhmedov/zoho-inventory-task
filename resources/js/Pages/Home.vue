<template>

    <div :class="isDropdownVisible ? 'overlay' : 'overlay_hide'" @click="toggleDropDown"></div>

    <div class="form-container">
        <div class="search-bar">
            <input type="text" placeholder="Select or add a customer" readonly
                   :value="selectedCustomer ? selectedCustomer.customer_name : ''"/>

            <button class="clear-button" v-if="selectedCustomer" @click="clearSelection">
                ×
            </button>
        </div>

        <div class="dropdown-suggestions">
            <div class="suggestion-item" :class="{ 'selected': isSelected(customer) }" @click="selectCustomer(customer)"
                 v-for="customer in customers" :key="customer.contact_id">
                <div class="customer-info">
                    <p class="name">{{ customer.customer_name }}</p>
                    <p class="email">✉️ {{ customer.email }}</p>
                </div>
            </div>
        </div>

        <div class="new-customer">
            <button class="add-button" @click="toggleDropDown">➕ New Customer</button>
        </div>
    </div>


    <div class="dropdown-form" v-if="isDropdownVisible">
        <div class="form-header">
            <h3>Add New Customer</h3>
            <button class="close-button" @click="toggleDropDown">×</button>
        </div>

        <form @submit.prevent="createCustomer">
            <div class="form-field">
                <label for="name">Name:</label>
                <input type="text" id="name" v-model="newCustomer.name" required/>
            </div>
            <div class="form-field">
                <label for="email">Email:</label>
                <input type="email" id="email" v-model="newCustomer.email" required/>
            </div>
            <div class="form-field">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" v-model="newCustomer.phone_number" required/>
            </div>
            <div class="form-actions">
                <button type="submit" class="submit-button">Submit</button>
                <button type="button" class="cancel-button" @click="toggleDropDown">Close</button>
            </div>
        </form>
    </div>


    <div class="item-grid">
        <div class="item" v-for="item in inventoryItems" :key="item.item_id">
            <div class="item-details">
                <p class="item-name">{{ item.name }}</p>
                <p class="item-price">Price {{ item.rate }}</p>
            </div>
            <div class="item-actions">
                <button class="quantity-button" @click="decreaseQuantity(item)">-</button>
                <span class="item-quantity"> {{ quantities[item.item_id] || 0 }}</span>
                <button class="quantity-button" @click="increaseQuantity(item)">+</button>
            </div>
        </div>
    </div>

    <div class="submit-section">
        <button class="submit-button" @click="createSalesOrder">Create Sales Order</button>
    </div>
</template>

<script setup>

import {onMounted, reactive, ref} from "vue";
import axios from "axios";
import {useForm} from "@inertiajs/vue3";

const customers = ref([]);
const inventoryItems = ref([]);

const isDropdownVisible = ref(false);

const newCustomer = useForm({
    name: '',
    email: '',
    phone_number: ''
});
const quantities = reactive({});


const selectedCustomer = ref(null);

const fetchCustomers = async () => {
    try {
        const response = await axios.get('/api/customers');
        customers.value = response.data;
    } catch (error) {
        alert(error);
        console.log(error);
    }
}

const fetchInventoryItems = async () => {
    try {
        const response = await axios.get('api/zoho/inventory-items');
        inventoryItems.value = response.data;
    } catch (error) {
        alert(error);
    }
}
const createCustomer = async () => {
    try {
        const payload = {
            name: newCustomer.name,
            email: newCustomer.email,
            phone: String(newCustomer.phone_number)
        };

        console.log(payload);
        const response = await axios.post('/api/customers', payload);

        if (response.status === 200 || response.status === 201) {
            newCustomer.reset();
            toggleDropDown();
            alert('Success!')
            await fetchCustomers();
        }
    } catch (error) {
        alert(error);
    }
}


const createSalesOrder = async () => {
    try {

        if (!selectedCustomer.value) {
            alert('Please select customer')
        }

        const items = inventoryItems.value
            .filter((item) => quantities[item.item_id] > 0)
            .map((item) => ({
                item_id: item.item_id,
                quantity: quantities[item.item_id],
                rate: item.rate,
            }));

        if (items.length === 0) {
            alert('Please add at least one item with a quantity greater than 0.');
            return;
        }


        const payload = {
            contact_id: selectedCustomer.value.contact_id,
            items: items
        }


        const response = await axios.post('/api/zoho/sales-order', payload);


        if (response.status === 200 || response.status === 201) {
            selectedCustomer.value = null;
        for (let itemId in quantities) {
                quantities[itemId] = 0
            }

            alert('Success!')
        }
    } catch (error) {
        alert(error);
    }
}

const toggleDropDown = () => {
    isDropdownVisible.value = !isDropdownVisible.value;
}

const selectCustomer = (customer) => {
    selectedCustomer.value = customer;
}
const isSelected = (customer) => {
    return selectedCustomer.value && selectedCustomer.value.contact_id === customer.contact_id;
}
const clearSelection = () => {
    selectedCustomer.value = null;
}


const increaseQuantity = (item) => {
    if (!quantities[item.item_id]) {
        quantities[item.item_id] = 0;
    }
    if (quantities[item.item_id] < item.available_stock) {
        quantities[item.item_id] += 1;
    }
}
const decreaseQuantity = (item) => {
    if (!quantities[item.item_id]) {
        quantities[item.item_id] = 0;
    }
    if (quantities[item.item_id] > 0) {
        quantities[item.item_id] -= 1;
    }
}

onMounted(() => {
    fetchCustomers();
    fetchInventoryItems();
})
</script>



