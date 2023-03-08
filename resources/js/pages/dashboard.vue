<script>
// import "@fullcalendar/core"; // solves problem with Vite
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import timeGridPlugin from "@fullcalendar/timegrid";
import rrulePlugin from "@fullcalendar/rrule";
import momentTimezonePlugin from "@fullcalendar/moment-timezone";

import download from '../composables/download'

import { mergeProps } from "vue";

import { RRule } from "rrule";

import { useEventStore } from "../store/event";
import axios from "axios";

export default {
  components: {
    FullCalendar, // ! make the <FullCalendar> tag available
  },
  setup() {
    // Events
    const eventStore = useEventStore();
    const { model: modelEvent, isEditing, eventModal, loading: eventLoading, calendarApi } = storeToRefs(eventStore);
    const { fetchEvents, saveEvent, deleteEvent } = eventStore;

    const calendarRef = ref()

    return {
      /* events */
      eventStore,
      eventModal,
      eventLoading,
      isEditing,
      modelEvent,
      saveEvent,
      fetchEvents,
      deleteEvent,

      /* Calendar */
      calendarRef,
      calendarApi,
    };
  },
  data() {
    return {
      activeView: null,
      loading: true,
      title: "",
      competencies: [],
      jobTitles: [],
      exportModal: false,
      exportJobTitle: null,


      // TODO get from user props
      currentTimeFormat: "H:i",
      currentDateFormat: "F j, Y",

      // * modal detection
      calendarOptions: {
        plugins: [
          dayGridPlugin,
          interactionPlugin,
          timeGridPlugin,
          // TODO still error
          momentTimezonePlugin,
          rrulePlugin,
        ],
        headerToolbar: null,
        // headerToolbar: {
        //   left: 'prev,next',
        //   center: 'title',
        //   right: 'today'
        // },

        // TODO get from current user
        // timeZone: "Asia/Jakarta",

        // * for all non-TimeGrid views
        dayMaxEventRows: true,

        views: {
          day: {
            dayMaxEventRows: false,
          },
        },
        eventDisplay: "block",
        initialView: "dayGridMonth",

        lazyFetching: false,
        editable: true,
        droppable: true,

        scrollTime: "00:00:00", // not scroll to current time e.q. on day view

        // Remove the top left all day text as it's not suitable
        allDayContent: (arg) => {
          arg.text = "";

          return arg;
        },

        moreLinkClick: (arg) => {
          console.log("more link", arg);
          this.calendarApi.gotoDate(arg.date);

          this.calendarApi.changeView("dayGridDay");
        },

        viewDidMount: (arg) => {
          console.log("view mount", arg);
          // this.title = arg.view.title;

          // We don't remember the dayGridDay as
          // this is the more link redirect view
          if (arg.view.type !== "dayGridDay") {
            this.rememberDefaultView(arg.view.type);
          }
        },

        eventClick: (info) => {
          console.log("event click", info.event);

          this.onEventClick(info.event);
        },

        dateClick: (data) => {
          console.log("date click", data);

          this.onDateClick(data);
        },


        eventResize: (resizeInfo) => {
          console.log("event resize", resizeInfo);

          this.onEventResize(resizeInfo);
        },

        eventDrop: (dropInfo) => {
          console.log("event droped", dropInfo);

          this.onEventDrop(dropInfo);
        },

        loading: (isLoading) => (this.loading = isLoading),

        events: (info, successCallback, failureCallback) => {
          const params = {
            start_date: moment(info.start).format("YYYY-MM-DD"),
            end_date: moment(info.end).format("YYYY-MM-DD"),
          };

          this.fetchEvents(params)
            .then(({ data }) => successCallback(this.prepareEventsForCalendar(data)))
            .catch((error) => {
              console.error(error);
            });
        },
      },
    };
  },
  methods: {
    mergeProps,
    onEventClick(event) {
      console.log(event);
      let model = {};

      if (event.extendedProps.isRepeated) {
        const rrule = this.hasRepeatedEvents(event);

        Object.assign(model, {
          start_date: rrule.start_date,
          start_time: rrule.start_time,
        });
      } else {
        Object.assign(model, {
          start_date: moment(event.startStr).format("YYYY-MM-DD"),
          start_time: moment(event.startStr).format("HH:mm"),
          end_date: moment(event.endStr).format("YYYY-MM-DD"),
          end_time: moment(event.endStr).format("HH:mm"),
        });
      }

      Object.assign(model, {
        title: event.title,
        event_id: parseInt(event.id),
        description: event.extendedProps.description,
        competency_id: event.extendedProps.competency_id,
        peoples: event.extendedProps.peoples,
        is_all_day: event.allDay,
        is_repeated: event.extendedProps.isRepeated ?? false,
        edit: false,
        show: true,
        loading: false,
      });

      this.eventStore.$patch((state) => {
        state.model = model;
        state.isEditing = true
      });
    },

    hasRepeatedEvents(event) {
      const rrule = RRule.fromString(event.extendedProps.rrule).origOptions;

      let rruleOptions = rrule;

      if (rrule["byweekday"]) {
        rruleOptions.byweekday = rrule["byweekday"].map((el) => el.weekday);
      }

      if (rrule["bymonth"]) {
        rruleOptions.bymonth = rrule["bymonth"];
      }

      const rruleDate = moment(rruleOptions.dtstart);

      const model = {
        start_date: rruleDate.format("YYYY-MM-DD"),
        start_time: rruleDate.format("HH:mm"),
      };

      this.eventStore.$patch((state) => {
        state.rrule = rruleOptions;
      });

      return model;
    },

    onEventDrop(dropInfo) {
      let payload = {};

      this.isEditing = true;

      const event = this.calendarApi.getEventById(dropInfo.event.id);

      if (dropInfo.event.allDay) {
        payload.start_date = dropInfo.event.startStr;

        payload.start_time = null;
        payload.end_time = null;
        // When dropping event from time column to all day e.q. on week view
        // there is no end date as it's the same day, for this reason, we need to update the
        // end date to be the same like the start date for the update request payload
        if (!dropInfo.event.end) {
          payload.end_date = payload.start_date;
        } else {
          // Multi days event, we will remove the one day to store
          // the end date properly in database as here for the calendar they are endDate + 1 day so they are
          // displayed properly see prepareEventsForCalendar method
          payload.end_date = this.endDateForStorage(dropInfo.event.endStr);
        }

        event.setExtendedProp("isAllDay", true);
        event.setEnd(this.endDateForCalendar(payload.end_date));
      } else {
        payload.start_date = moment(dropInfo.event.startStr).format("YYYY-MM-DD");
        payload.start_time = moment(dropInfo.event.startStr).format("HH:mm");
        // When dropping all day event to non all day e.q. on week view from top to the timeline
        // we need to update the end date as well
        if (dropInfo.oldEvent.allDay && !dropInfo.event.allDay) {
          let endDateStr = moment(dropInfo.event.startStr)
            .add(1, "hours")
            .format("YYYY-MM-DD HH:mm:ss");
          payload.end_date = moment(endDateStr).format("YYYY-MM-DD");
          payload.end_time = moment(endDateStr).format("HH:mm");

          event.setEnd(endDateStr);
          event.setExtendedProp("hasEndTime", true);
        } else {
          // We will check if the actual endStr is set, if not will use the start dates as start time
          // because this may happen when the activity start and end
          // date are the same, in this case, fullcalendar does not provide the endStr
          payload.end_date = dropInfo.event.endStr
            ? moment(dropInfo.event.endStr).format("YYYY-MM-DD")
            : payload.start_date;

          // Time can be modified on week and day view, on month view we will
          // only modify the time on actual calendar with time
          if (
            this.activeView !== "dayGridMonth" ||
            dropInfo.event.extendedProps.hasEndTime
          ) {
            payload.end_time = dropInfo.event.endStr
              ? moment(dropInfo.event.endStr).format("HH:mm")
              : payload.start_time;
            event.setExtendedProp("hasEndTime", true);
          }
        }

        event.setExtendedProp("isAllDay", false);
      }
      payload.event_id = dropInfo.event.id;
      payload.title = dropInfo.event.title;

      this.saveEvent(payload);
    },

    /**
     * Handle event resize.
     *
     * @param  {object} resizeInfo event properties
     *
     * @return {Void}
     */
    onEventResize(resizeInfo) {
      let payload = {};

      this.isEditing = true;

      if (resizeInfo.event.allDay) {
        payload = {
          start_date: resizeInfo.event.startStr,
          end_date: this.endDateForStorage(resizeInfo.event.endStr),
        };
      } else {
        payload = {
          start_date: moment(resizeInfo.event.startStr).format("YYYY-MM-DD"),
          start_time: moment(resizeInfo.event.startStr).format("HH:mm"),
          end_date: moment(resizeInfo.event.endStr).format("YYYY-MM-DD"),
          end_time: moment(resizeInfo.event.endStr).format("HH:mm"),
        };
      }
      payload.event_id = resizeInfo.event.id;
      payload.title = resizeInfo.event.title;

      this.saveEvent(payload);
    },

    /**
     * Handle date click.
     *
     * @param  {object} data date properties
     *
     * @return {Void}
     */
    onDateClick(data) {
      const date = data.allDay
        ? data.dateStr
        : moment.utc(data.dateStr).format("YYYY-MM-DD");
      const time = data.allDay ? null : moment.utc(data.dateStr).format("HH:mm");

      const model = {
        // for default calendar selection

        is_repeated: false,

        start_date: date,
        start_time: time,
        // On end date, we will format with the user timezone as the end date
        // has not time when on dateClick click and for this reason, we must get the actual date
        // to be displayed in the create modal e.q. if user click on day view 19th April 12 AM
        // the startDate will be shown properly but not the end date as if we format the end date
        // with UTC will 18th April e.q. 18th April 22:00 (UTC)

        end_date: date,
        end_time: time,
      };

      this.eventStore.$patch((state) => {
        state.model = model;
        state.eventModal = true;
      });
    },

    /**
     * Change the calendar view
     *
     * @param  {String} viewName
     *
     * @return {Void}
     */
    changeView(viewName) {
      this.calendarApi.changeView(viewName);
      this.activeView = viewName;
      this.rememberDefaultView(viewName);
    },

    /**
     * Create end date for the calendar
     *
     * @see  prepareEventsForCalendar
     *
     * @param  {mixed} date
     * @param  {String} format
     *
     * @return {String}
     */
    endDateForCalendar(date, format = "YYYY-MM-DD") {
      return moment(date).add("1", "days").format(format);
    },

    /**
     * Create end date for storage
     *
     * @see  prepareEventsForCalendar
     *
     * @param  {mixed} date
     * @param  {String} format
     *
     * @return {String}
     */
    endDateForStorage(date, format = "YYYY-MM-DD") {
      return moment(date).subtract("1", "days").format(format);
    },

    /**
     * Format the event title
     *
     * @see https://fullcalendar.io/docs/event-render-hooks
     *
     * @param  {Object} arg
     *
     * @return {Object}
     */
    createEventTitleDomNodes(arg) {
      let event = document.createElement('span');

      if (arg.event.allDay) {
        event = arg.event.title;
      } else {
        let momentInstanceStartTime = moment(arg.event.startStr);
        let startTime = momentInstanceStartTime.format(
          moment().PHPconvertFormat(this.currentTimeFormat)
        );
        let momentInstanceEndTime;

        if (arg.isMirror && arg.isDragging && arg.event.extendedProps.isAllDay) {
          // Dropping from all day to non-all day
          // In this case, there is no end date, we will automatically add 1 hour to the start date
          momentInstanceEndTime = moment(arg.event.startStr).add(1, "hours");
        } else if (
          ((arg.isMirror && arg.isResizing) ||
            (arg.isMirror && arg.isDragging) ||
            (arg.event.endStr && arg.event.extendedProps.hasEndTime === true)) &&
          // This may happen when the activity due and end
          // date are the same, in this case, fullcalendar does not provide the endStr
          // attribute and the time will be shown only from the startStr
          arg.event.endStr != arg.event.startStr
        ) {
          momentInstanceEndTime = moment(arg.event.endStr);
        }

        if (momentInstanceEndTime) {
          let endTime = momentInstanceEndTime.format(
            moment().PHPconvertFormat(this.currentTimeFormat)
          );
          if (momentInstanceEndTime.date() != momentInstanceStartTime.date()) {
            startTime += " - " + endTime + " " + momentInstanceEndTime.format("MMM D");
          } else {
            startTime += " - " + endTime;
          }
        }

        event = startTime + " " + arg.event.title;
      }

      return event


      /*       return {
              domNodes: [event],
            }; */
    },

    /**
     * Prepare the given events for calendar
     *
     * @param  {Array} events
     *
     * @return {Array}
     */
    prepareEventsForCalendar(events) {
      events.map((event) => {
        // @see https://stackoverflow.com/questions/30323397/fullcalendar-event-shows-wrong-end-date-by-one-day
        // @see https://fullcalendar.io/docs/event-parsing
        // e.q. event with start 2021-04-01 and end date 2021-04-03 in the calendar is displayed
        // from 2021-04-01 to 2021-04-02, in this case on fetch, we will add 1 days so they are
        // displayed properly and on update, we will remove 1 day so they are saved properly
        event.extendedProps.isAllDay = event.allDay;

        if (event.extendedProps.isRepeated) {
          event.rrule = RRule.fromString(event.extendedProps.rrule).origOptions;
          event.rrule.tzid = this.calendarOptions.timeZone;

          // disable drag and drop on recursive events
          event.editable = false;
        }
        if (event.allDay) {
          event.end = this.endDateForCalendar(event.end);
        } else if (!/\d{4}-\d{2}-\d{2}\T?\d{2}:\d{2}:\d{2}$/.test(event.end)) {
          // no end time, is not in y-m-dTh:i:s format
          // to prevent clogging the calendar with events showing
          // over the week/day view, we will just add the start hour:minute
          // as end hour:minute + 30 minutes to be shown in one simple box
          // this can usually happen when to start and the end date are the same and there is no end time
          event.end = moment(event.end);
          const momentStart = moment(event.start);
          event.end
            .hour(momentStart.hour())
            .minute(momentStart.minute())
            .second(0)
            .add(30, "minutes");
          event.end = event.end.format("YYYY-MM-DD\THH:mm:ss");
          event.extendedProps.hasEndTime = false;
        } else {
          event.extendedProps.hasEndTime = true;
        }

        // We need to set endEditable on events displayed on the month view as for some reason
        // when the calendar option {editable: true} is set the month view events are not resizable
        // note this is only applicable for all day events as non-all days events cannot be dragged
        // on month view (fullcalendar limitation)
        if (this.activeView === "dayGridMonth") {
          event.endEditable = true;
        }

        if (event.isReadOnly) {
          event.editable = false;
        }

        return event;
      });

      return events;
    },

    /**
     * Refresh events
     *
     * @return {Void}
     */
    refreshEvents() {
      this.calendarApi.refetchEvents();
    },

    /**
     * Remember the default view to storage
     *
     * @param  {String} view
     *
     * @return {Void}
     */
    rememberDefaultView(view) {
      return localStorage.setItem("default-calendar-view", view);
    },

    /**
     * Set the calendar default view
     */
    setDefaultView() {
      return localStorage.getItem("default-calendar-view") || "timeGridWeek"
    },
    next() {
      this.calendarApi.next();
      this.title = this.calendarApi.view.title;
    },
    prev() {
      this.calendarApi.prev();
      this.title = this.calendarApi.view.title;
    },
    today() {
      this.calendarApi.today();
      this.title = this.calendarApi.view.title;
    },
    async exportToExcel(id) {
      try {
        const response = await axios.get('api/peoples/export/'+id, { responseType: 'blob' })

        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        const filename = new Date().toISOString() + '.xls'

        link.setAttribute('download', filename) //or any other extension
        document.body.appendChild(link)
        link.click()

      } catch (error) {
        console.error(error);
      }
    }
  },
  mounted() {
    this.setDefaultView();

    axios.get('/api/competencies').then(({ data }) => {
      this.competencies = data
    })

    axios.get('/api/job-title/all').then(({ data }) => {
      this.jobTitles = data
    })

    if (this.calendarRef) {
      this.calendarApi = this.calendarRef.getApi();
    }

    this.title = this.calendarApi.view.title;
  },
};
</script>

<template>
  <Teleport to="#page-header">
    <div  class="d-flex justify-space-between align-center px-4">
      <v-btn-toggle variant="outlined" divided group color="primary">
        <v-btn :rounded="false" color="primary" @click="prev()" icon="mdi-chevron-left" > </v-btn>
        <v-btn :rounded="false" color="primary" @click="today()"> Today </v-btn>
        <v-btn :rounded="false" color="primary" @click="next()" icon="mdi-chevron-right"> </v-btn>
      </v-btn-toggle>
      <div> <h3>{{ title }}</h3> </div>
      <v-btn :rounded="false" @click="exportModal=true" prepend-icon="mdi-file-excel"> export </v-btn>
    </div>
  </Teleport>

  <v-container fluid>
    <v-card class="pa-4">

      <FullCalendar ref="calendarRef" :options="calendarOptions" class="h-screen">

        <template #eventContent="arg">
          <v-menu :key="arg.event.id" :close-on-content-click="false" location="end">
            <template v-slot:activator="{ props: menu }">
              <v-tooltip bottom color="teal">
                <template v-slot:activator="{ props: tooltip }">
                  <div style="min-height:20px;" v-bind="mergeProps(menu, tooltip)"
                    :id="`event-activator-${arg.event.id}`" @click="onEventClick(arg.event)">
                    {{ createEventTitleDomNodes(arg) }}
                  </div>
                </template>
                <div style="text-align:left;">
                  Title: {{ arg.event.title }}<br />
                  Description: {{ arg.event.extendedProps.description }}<br />
                  Time: {{ arg.event.start }}<br />
                </div>
              </v-tooltip>

            </template>
            <v-card class="pa-4" width="480" :disabled="modelEvent.loading" :loading="modelEvent.loading">
              <v-card-item>
                <v-card-title> {{ modelEvent.title }} </v-card-title>
                <v-card-subtitle> {{ modelEvent.start_date }} - {{ modelEvent.start_time }} </v-card-subtitle>
              </v-card-item>


              <v-card-text>
                <v-text-field v-model="modelEvent.title" label="Title"></v-text-field>
                <v-textarea rows="1" row-height="20" v-model="modelEvent.description" label="Description"></v-textarea>
                <v-checkbox label="All Day" v-model="modelEvent.is_all_day" color="primary"> </v-checkbox>
                <v-row>
                  <v-col :cols="modelEvent.is_all_day ? 12 : 6">
                    <v-text-field type="date" v-model="modelEvent.start_date" label="Start Date"></v-text-field>
                  </v-col>
                  <v-col v-if="!modelEvent.is_all_day" :cols="6">
                    <v-text-field type="time" v-if="!modelEvent.is_all_day" v-model="modelEvent.start_time"
                      label="Start Time"></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col :cols="modelEvent.is_all_day ? 12 : 6">
                    <v-text-field type="date" v-model="modelEvent.end_date" label="End Date"></v-text-field>
                  </v-col>
                  <v-col v-if="!modelEvent.is_all_day" :cols="6">
                    <v-text-field type="time" v-if="!modelEvent.is_all_day" v-model="modelEvent.end_time"
                      label="End Time"></v-text-field>
                  </v-col>
                </v-row>

              </v-card-text>


              <v-card-text>
                <v-list-subheader> Attendance Lists: </v-list-subheader>
                <v-list lines="two" max-height="200">
                  <v-list-item v-for="(people, index) in modelEvent.peoples" :key="people.id">
                    <template v-slot:prepend>
                      <v-avatar color="primary">
                        <span class="text-h5">
                          {{ index+ 1 }}

                        </span>
                      </v-avatar>
                    </template>
                    <v-list-item-title> {{ people.name }} </v-list-item-title>
                    <v-list-item-subtitle> {{ people.nik }} / {{ people.org }} </v-list-item-subtitle>

                  </v-list-item>
                </v-list>

              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn color="error" :rounded="false" :loading="modelEvent.loading" variant="text"
                  @click="deleteEvent(arg.event.id)">
                  Delete
                </v-btn>
                <v-btn color="primary" :rounded="false" :loading="modelEvent.loading" variant="flat"
                  @click="saveEvent(modelEvent)">
                  Save
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-menu>




        </template>
      </FullCalendar>


      <v-dialog v-model="eventModal">
        <v-card class="pa-4" width="480" :disabled="modelEvent.loading" :loading="modelEvent.loading">
          <v-card-title>Add Event </v-card-title>
          <v-card-text>
            <v-text-field v-model="modelEvent.title" label="Title"></v-text-field>
            <v-textarea rows="1" row-height="20" v-model="modelEvent.description" label="Description"></v-textarea>
            <v-select label="Competency" v-model="modelEvent.competency_id" :items="competencies" item-value="id"
              item-title="name"> </v-select>
            <v-checkbox label="All Day" v-model="modelEvent.is_all_day" color="primary"> </v-checkbox>
            <v-row>
              <v-col :cols="modelEvent.is_all_day ? 12 : 6">
                <v-text-field type="date" v-model="modelEvent.start_date" label="Start Date"></v-text-field>
              </v-col>
              <v-col v-if="!modelEvent.is_all_day" :cols="6">
                <v-text-field type="time" v-if="!modelEvent.is_all_day" v-model="modelEvent.start_time"
                  label="Start Time"></v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col :cols="modelEvent.is_all_day ? 12 : 6">
                <v-text-field type="date" v-model="modelEvent.end_date" label="End Date"></v-text-field>
              </v-col>
              <v-col v-if="!modelEvent.is_all_day" :cols="6">
                <v-text-field type="time" v-if="!modelEvent.is_all_day" v-model="modelEvent.end_time"
                  label="End Time"></v-text-field>
              </v-col>
            </v-row>

          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>

            <v-btn color="error" :rounded="false" :loading="modelEvent.loading" variant="text"
              @click="eventModal = false">
              Cancel
            </v-btn>
            <v-btn color="primary" :rounded="false" :loading="modelEvent.loading" variant="flat"
              @click="saveEvent(modelEvent)">
              Save
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="exportModal">
        <v-card class="pa-4" width="480" >
          <v-card-title>Export per JobTitle </v-card-title>
          <v-card-text>
            <v-autocomplete :items="jobTitles" item-value="id" item-title="name" v-model="exportJobTitle" label="Export" placeholder="Job Title to Export"> </v-autocomplete>
          
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>

            <v-btn color="error" :rounded="false"  variant="text"
              @click="exportModal = false">
              Cancel
            </v-btn>
            <v-btn color="primary" :rounded="false" variant="flat"
              @click="exportToExcel(exportJobTitle)">
              Export
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

    </v-card>
  </v-container>

</template>

<style>
.fc .fc-toolbar.fc-header-toolbar {
  margin-bottom: 0;
  padding-top: 8px;
  padding-bottom: 8px;
}

.fc .fc-button .fc-icon {
  vertical-align: top !important;
  ;
}

.fc .fc-toolbar-title {
  font-size: 1.07em;
  font-weight: 600;
}

.fc .fc-event-main {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.dark .fc-col-header-cell,
.dark .fc-toolbar-title,
.dark .fc-timegrid-slot-label-frame,
.dark .fc-daygrid-day-number {
  color: rgb(var(--color-neutral-200));
}

.dark .fc-theme-standard .fc-scrollgrid,
.dark .fc-theme-standard td,
.dark .fc-theme-standard th {
  border-color: rgb(var(--color-neutral-700));
}
</style>


<route lang="yaml">
  name: dashboard
  meta:
    title: Dashboard
    requiresAuth: true
</route>