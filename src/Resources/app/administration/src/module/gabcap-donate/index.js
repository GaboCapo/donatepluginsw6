import './page/gabcap-donate-list';
import './page/gabcap-donate-detail';
import './page/gabcap-donate-create';
import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

const { Module } = Shopware;

Module.register('gabcap-donate', {
    type: 'plugin',
    name: 'Donate',
    title: 'gabcap-donate.general.mainMenuItemGeneral',
    description: 'sw-property.general.descriptionTextModule',
    color: '#ff3d58',
    icon: 'default-basic-shape-heart',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        list: {
            component: 'gabcap-donate-list',
            path: 'list'
        },
        detail: {
            component: 'gabcap-donate-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'gabcap.donate.list'
            }
        },
        create: {
            component: 'gabcap-donate-create',
            path: 'create',
            meta: {
                parentPath: 'gabcap.donate.list'
            }
        }
    },

    navigation: [{
        label: 'gabcap-donate.general.mainMenuItemGeneral',
        color: '#ff3d58',
        path: 'gabcap.donate.list',
        icon: 'default-basic-shape-heart',
        position: 100
    }]
});
