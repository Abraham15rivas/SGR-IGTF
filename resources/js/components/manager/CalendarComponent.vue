<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">¡ Bienvenido ! aquí puedes colocar las fechas feriadas en el calendario</div>
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
