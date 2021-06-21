<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg-bav-ligth">
                <div class="card-header card-header-bav">Banco Agrícola de Venezuela, C.A., Banco Universal</div>
                <div class="card-body">
                    <welcome-component />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Swal from 'sweetalert2'
    export default {
        props: {
            notifications: Array
        },
        methods: {
            loadNotification(object) {
                let html = `Ninguna notificación detectada`
                
                if(object) {
                    const errors = Object.values(object)
                    let ul = document.createElement("ul")
                    let li = ``
                    html = ``

                    errors.forEach(element => {
                        li = document.createElement("li")
                        li.innerHTML = element.description
                        ul.appendChild(li)
                    })
                    html += ul.innerHTML
                }

                return html
            },
            async confirm() {
                try {
                    const url = `/admin/confirm/notification`
                    let response = await axios.get(url)
                } catch (error) {
                    console.log(error)
                }
            }
        },
        mounted() {
            console.log('Component mounted.')
            if(this.notifications.length > 0) {
                Swal.fire({
                    icon: 'success',
                    title: `Nuevas notificaciones`,
                    html: `<span>${ this.loadNotification(this.notifications) }</span>`,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Entendido!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.confirm()
                        Swal.fire(
                            'Notificación!',
                            'Aceptada correctamente',
                            'success'
                        )
                    }
                })
            }
        }
    }
</script>
