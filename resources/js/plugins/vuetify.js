
import '@mdi/font/css/materialdesignicons.css' // Ensure you are using css-loader
// Styles
import 'vuetify/styles'

import { md3 } from 'vuetify/blueprints'

import { createVuetify } from 'vuetify'

import { VDataTableServer } from 'vuetify/labs/VDataTable'

export default createVuetify({
  components:{
    VDataTableServer,
  },
  defaults: {
    VDataTable: {
      fixedHeader: true,
      noDataText: 'Results not found',
    },
  },
  blueprint: md3,
  icons: {
    defaultSet: 'mdi',
  },
  theme: {
    themes: {
      light: {
        colors: {
          primary: '#1867C0',
          secondary: '#5CBBF6',
        },
      },
    },
  },
})