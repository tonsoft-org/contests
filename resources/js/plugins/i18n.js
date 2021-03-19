import Vue from 'vue';
import VueI18n from 'vue-i18n';

Vue.use(VueI18n);

const messages = {
    'en': {
        "Home": "Home",
        "OFF-chain": 'OFF-chain',
        "ON-chain": 'ON-chain',
    },
    'ru': {
        "Home": "Главная",
        "OFF-chain": 'OFF-цепи',
        "ON-chain": 'ON-цепи',
    }
};

const i18n = new VueI18n({
    locale: 'en',
    fallbackLocale: 'en',
    messages,
});

export default i18n;
