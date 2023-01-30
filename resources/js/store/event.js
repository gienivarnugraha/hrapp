const url = '/api/events'

export const useEventStore = defineStore('events', {
  state: () => ({
    model: {
      start_date: null,
      start_time: null,
      end_date: null,
      end_time: null,
      title: null,
      description: null,
      is_all_day: false,
      event_id: null,
      is_repeated: false,
      rrule: null,
    },
    rrule: {},
    isEditing: false,
    eventModal: false,
  }),
  getters: {
    getFullStartDate: (state) => {
      const startDate = state.model.start_date ?? moment().utc().format("YYYY-MM-DD");
      const startTime = state.model.start_time ?? moment().utc().format("HH:mm");

      return `${startDate}T${startTime}`
    },
  },
  actions: {
    async fetchEvents(params = null) {
      try {
        return await axios.get(url, { params })
      } catch (error) {
        console.error('error fetching events: ', error);
      }
    },
    async saveEvent(payload) {
      const {  event_id, ...rest } = payload

      try {
        let response
        if (event_id && this.isEditing) {
          response = await axios.put(`${url}/${event_id}`, {...rest });
        } else {
          response = await axios.post(`${url}`, rest);
        }

        this.isEditing = false;

        return response.data
      } catch (error) {
        console.error(error);
      }
    },
    async deleteEvent(payload) {
      const {event_id} = payload

      try {
        this.isEditing = false;

        const { data } = await axios.delete(`${url}/${event_id}`)

        return data

      } catch (error) {
        console.error(error);
      }
    }
  }
})
