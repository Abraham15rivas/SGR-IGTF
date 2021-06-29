<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg-bav-ligth">
                <div class="card-header card-header-bav">
                  <span>¡ Bienvenido ! aquí puedes colocar las fechas feriadas en el calendario</span>
                </div>
                <div class="card-body">
                  <FullCalendar :options="calendarOptions" />
                </div>
            </div>
             <!-- Ventanas modal -->
            <new-date-modal-component 
              @refreshCalendar="refreshCalendar"
              @emptyDate="date = null"
              :date="date"
            />
        </div>
    </div>
</template>

<script>
  import Swal from 'sweetalert2'
  import FullCalendar from '@fullcalendar/vue'
  import dayGridPlugin from '@fullcalendar/daygrid'
  import interactionPlugin from '@fullcalendar/interaction'
  import NewDateModalComponent from './NewDateModalComponent'

  export default {
    props: ['dates'],
    components: {
      FullCalendar, // make the <FullCalendar> tag available
      'new-date-modal-component': NewDateModalComponent,
    },
    data() {
      return {
        calendarOptions: {
          plugins: [ dayGridPlugin, interactionPlugin ],
          initialView: 'dayGridMonth',
          selectable: true,
          dayMaxEvents: true,
          select: this.handleDateSelect,
          eventClick: this.handleEventClick,
          eventsSet: this.handleEvents,
          events: []
        },
        currentEvents: [],
        calendarView: null,
        date: null
      }
    },
    methods: {
      refreshCalendar(value) {
        let calendarApi = this.calendarView
        calendarApi.unselect()

        if (value) {
          calendarApi.addEvent({
            id: value.id,
            title: value.title,
            description: value.description,
            status: value.status,
            date: value.date
          })

          this.calendarView = null
        }
      },
      handleDateSelect(selectInfo) {
        this.date = selectInfo.startStr
        this.calendarView = selectInfo.view.calendar
        $('#new_date').modal('show')
      },
      handleEventClick(clickInfo) {
        Swal.fire({
          title: `¿Deseas borrar el siguiente evento: ${ clickInfo.event.title } del calendario?`,
          text: "¡No podrás revertir esto!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, Borrar!'
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire(
              '¡Borrado!',
              'Fecha borrada correctamente',
              'success'
            )
            this.deleteDate(clickInfo.event.id)
            clickInfo.event.remove()
          }
        })
      },
      handleEvents(events) {
        this.currentEvents = events
      },
      async deleteDate(id) {
        try {
          let url = `/manager/calendar/delete/${ id }`
          const response = await axios.delete(url)
        } catch (error) {
          console.log(error)
        }
      }
    },
    mounted() {
      this.calendarOptions.events = this.dates
    }
  }
</script>
