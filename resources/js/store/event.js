import { toRaw } from "vue";

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
      competency_id: null,
    },
    isEditing: false,
    rrule: {},
    calendarApi: null,

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
      const { event_id, ...rest } = payload
      this.model.loading = true

      try {
        let response
        if (event_id && this.isEditing) {
          response = await axios.put(`${url}/${event_id}`, { ...rest });
        } else {
          response = await axios.post(`${url}`, rest);
        }

        this.isEditing = false;

        this.eventModal = false;

        this.calendarApi.refetchEvents()

        return response.data
      } catch (error) {
        console.error(error);
      } finally {
        this.model.loading = false
      }
    },
    async deleteEvent(id) {
      this.model.loading = true
      try {

        const { data } = await axios.delete(`${url}/${id}`)

        this.calendarApi.refetchEvents()

        this.isEditing = false;

        this.eventModal = false;

        return data

      } catch (error) {
        console.error(error);
      } finally {
        this.model.loading = false
      }
    },
    async saveAttendees(eventId, payload) {
      this.model.loading = true

      let attendance = toRaw(payload).reduce(function (map, obj) {
        map[obj.id] = { 'attended': obj.pivot.attended };
        return map;
      }, {});

      try {
        const { data } = await axios.put(`${url}/${eventId}/attendance`, { attendance })

        this.calendarApi.refetchEvents()

        return data

      } catch (error) {
        console.error(error);
      } finally {
        this.model.loading = false
      }
    }
  }
})
