<template>
    <div class="modal fade" id="new_date" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content bg-bav-ligth">
                <div class="modal-header card-header-bav">
                    <h5 class="modal-title" id="exampleModalLabel">Datos del usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="clearForm">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_event" method="post">
                        <div class="form-group row">
                            <label for="title" class="col-md-4 col-form-label text-md-right">Título</label>
                            <div class="col-md-6">
                                <input id="title" type="text" class="form-control" name="title" v-model="event.title" required autocomplete="title" autofocus>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-md-4 col-form-label text-md-right">Descripción</label>
                            <div class="col-md-6">
                                <input id="description" type="text" class="form-control" name="description" v-model="event.description" required autocomplete="description">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="status" class="col-md-4 col-form-label text-md-right">Status</label>
                            <div class="col-md-6">
                                <select class="form-control" name="status" id="status" v-model="event.status">
                                    <option value="1">Acivo</option>
                                    <option value="0">Desactivo</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer text-right">
                    <button  type="button" class="btn btn-secondary" data-dismiss="modal" @click="clearForm">Cerrar</button>
                    <button  type="submit" class="btn btn-success" @click="setHoliday" v-text="`Guardar`"></button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapActions } from 'vuex'
    import Swal from 'sweetalert2'

    export default {
        props: ['date'],
        data() {
            return {
                event: {
                    title: null,
                    description: null,
                    date: null,
                    status: 0
                }
            }
        },
        watch: {
            date() {
                if(this.date != null) {
                    this.event.date = this.date
                }
            }
        },
        methods: {
            clearForm() {
                // Clear form
                document.getElementById("form_event").reset()
                
                // Clear variables
                this.event.title        = null
                this.event.description  = null
                this.event.date         = null
                this.event.status       = 0

                // vaciar fecha
                this.$emit('emptyDate')
            },
            async setHoliday() {
                try {
                    if(
                        this.event.title == null ||
                        this.event.description == null ||
                        this.event.status == null ||
                        this.date == null
                    ) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: `Debe rellenar todos los campos`,
                        })
                    } else {

                        let url = `/manager/calendar/store`
                        let params = new FormData(
                            document.getElementById('form_event')
                        )
                        params.append("date", this.event.date)
                       
                        let response = await axios.post(url, params)
                        const object = response.data
                        if(object.success == true) {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: `${ object.message }`,
                                showConfirmButton: false,
                                timer: 1500
                            })
                            
                            // Refresh Calendaar
                            this.$emit('refreshCalendar', {
                                id: object.date.id,
                                title: this.event.title,
                                description: this.event.description,
                                status: this.event.status,
                                date: this.event.date
                            })

                            // Clear form and variables
                            this.clearForm()

                            // Close modal window
                            setTimeout(() => $('#new_date').modal('hide'), 2000)
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: `${ object.message }`,
                                html: `<span>${ this.loadError(object.errors) }</span>`,
                            })
                        }
                    }
                } catch (error) {
                    console.log(error)
                }
            },
            loadError(object) {
                let html = `Ningún error detectado`
                
                if(object) {
                    const errors = Object.values(object)
                    let ul = document.createElement("ul")
                    let li = ``
                    html = ``

                    errors.forEach(element => {
                        element.forEach(ele => {
                            li = document.createElement("li")
                            li.innerHTML = ele
                            ul.appendChild(li)
                        })
                    })
                    html += ul.innerHTML
                }

                return html
            },
        }
    }
</script>