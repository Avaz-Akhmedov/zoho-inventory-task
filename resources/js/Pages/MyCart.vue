<template>
    <div class="cart-container">
        <h2>🛒 My Cart</h2>

        <ul v-if="cart.length" class="cart-list">
            <li v-for="item in cart" :key="item.item_id" class="cart-item">
                <div class="item-details">
                    <span class="item-name">{{ item.name }}</span>
                    <span class="item-quantity">Quantity: {{ item.quantity }}</span>
                </div>
                <div class="item-price">
                    ${{ item.rate }} × {{ item.quantity }} = ${{ item.quantity * item.rate }}
                </div>

                <button class="remove-button" @click="removeFromCart(item)"></button>
            </li>
        </ul>

        <p v-else class="empty-cart-message">Your cart is empty.</p>

        <a href="/checkout" class="checkout-button" >Checkout</a href="/checkout">

    </div>
</template>


<script setup>
import {cart} from '../Stores/сart.js'


const removeFromCart = (item) => {
    const index = cart.value.findIndex((i) => i.item_id === item.item_id);

    if (index !== -1) {
        cart.value.splice(index, 1);
    }
}

</script>
