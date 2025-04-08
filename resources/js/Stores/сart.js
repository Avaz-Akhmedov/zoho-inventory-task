import {ref, watch} from 'vue'

export const cart = ref(JSON.parse(localStorage.getItem('cart')) || []);
export const addToCart = (item, itemQuantity) => {
    if (itemQuantity <= 0) return false;

    const existingIndex = cart.value.findIndex(i => i.item_id === item.item_id)

    if (existingIndex !== -1) {
        cart.value[existingIndex].quantity = itemQuantity
    } else {
        cart.value.push({
            item_id: item.item_id,
            name: item.name,
            rate: item.rate,
            quantity: itemQuantity
        })
    }
    return true;
}

watch(cart, (newCart) => {
    localStorage.setItem('cart', JSON.stringify(newCart))
}, { deep: true })

