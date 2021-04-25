<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg-bav-ligth">
                <div class="card-header card-header-bav">¡ Bienvenido ! aquí puedes colocar las fechas feriadas en el calendario</div>
                <div class="card-body">
                  <FullCalendar :options="calendarOptions" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import FullCalendar from '@fullcalendar/vue'
  import dayGridPlugin from '@fullcalendar/daygrid'
  import interactionPlugin from '@fullcalendar/interaction'

  export default {
    props: ['dates'],
    components: {
      FullCalendar // make the <FullCalendar> tag available
    },
    data() {
      return {
        calendarOptions: {
          plugins: [ dayGridPlugin, interactionPlugin ],
          initialView: 'dayGridMonth'
        }
      }
    }
  }
</script>
