import axios from 'axios';

export const fetchCustomers = async () => {
    try {
        const response = await axios.get('/api/customers');
        return response.data;
    } catch (error) {
        console.error('Error fetching customers:', error);
        throw error;
    }
};

export const fetchInventoryItems = async () => {
    try {
        const response = await axios.get('/api/zoho/inventory-items');
        return response.data;
    } catch (error) {
        console.error('Error fetching inventory items:', error);
        throw error;
    }
};

export const createCustomer = async (payload) => {
    try {
        return await axios.post('/api/customers', payload);
    } catch (error) {
        console.error('Error creating customer:', error);
        throw error;
    }
};

export const createSalesOrder = async (payload) => {
    try {
        return await axios.post('/api/zoho/sales-order', payload);
    } catch (error) {
        console.error('Error creating sales order:', error);
        throw error;
    }
};
