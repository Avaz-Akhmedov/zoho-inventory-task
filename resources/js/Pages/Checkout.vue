<template>
    <div class="form-container">
        <form v-if="cart.length" @submit.prevent="submitForm" class="crm-form">
            <h2>Create account and sales order</h2>

            <label for="name">Account name:</label>
            <input
                type="text"
                id="name"
                placeholder="Enter your name"
                v-model="form.name"
                class="form-input"
            />
            <div v-if="form.errors.name" class="error-message">
                {{ form.errors.name }}
            </div>

            <label for="email">Account Email:</label>
            <input
                type="email"
                id="email"
                placeholder="Enter your email"
                v-model="form.email"
                class="form-input"
            />
            <div v-if="form.errors.email" class="error-message">
                {{ form.errors.email }}
            </div>

            <label for="phone_number">Account Phone number:</label>
            <input
                type="number"
                id="phone_number"
                placeholder="Enter your phone number"
                v-model="form.phone_number"
                class="form-input"
            />
            <div v-if="form.errors.phone_number" class="error-message">
                {{ form.errors.phone_number }}
            </div>

            <button type="submit" class="submit-button">
                <span>{{ form.processing ? 'Submitting' : 'Submit' }}</span>
            </button>
        </form>

        <h1 v-else>Please add something to cart</h1>
    </div>
</template>

<script setup>

import {useForm} from "@inertiajs/vue3";
import {cart} from '../Stores/сart.js';
import axios from "axios";

const form = useForm({
    name: '',
    email: '',
    phone_number: ''
})


const validateForm = () => {

    form.clearErrors();
    let isValid = true;

    if (!form.name.trim()) {
        form.setError('name', 'The name field is required');
        isValid = false;
    }

    if (!form.email.trim()) {
        form.setError('email', 'The email field is required');
        isValid = false;
    }

    const phoneNumber = String(form.phone_number).trim();
    if (!phoneNumber) {
        form.setError('phone_number', 'The phone number field is required');
        isValid = false;
    } else {
        const phonePattern = /^\+?[0-9\s\-]{10,}$/;
        if (!phonePattern.test(phoneNumber)) {
            form.setError('phone_number', 'Please enter a valid phone number');
            isValid = false;
        }
    }

    return isValid;
}

const submitForm = async () => {
    if (!validateForm()) return;

    try {
        const payload = {
            customer: {
                name: form.name,
                email: form.email,
                phone: String(form.phone_number)
            },
            items: cart.value.map((item) => ({
                item_id: item.item_id,
                quantity: item.quantity,
                rate: item.rate,
            })),
        };
        const response = await axios.post('/api/zoho/sales-order', payload);

        if (response.status === 200) {
            alert('Success!')
            cart.value = [];
        }
    } catch (err) {
        console.log(err);
        alert('An unexpected error occurred. Please try again later.');
    }

}
</script>
