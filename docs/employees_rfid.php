<?php
 ?>
<?php
include_once "header.php";
include_once "nav.php";
?>
<div class="row" id="app">
    <div class="col-12">
        <h1 class="text-center">Emparejamiento RFID </h1>
    </div>
    <div class="col-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>
                            Empleado
                        </th>
                        <th>
                            RFID Serial
                        </th>
                        <th>
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="employee in employees">
                        <td>{{employee.name}}</td>
                        <td>

                            <h4 v-if="employee.rfid_serial"><span class="badge badge-success"><i class="fa fa-check"></i>&nbsp;Asignado ({{employee.rfid_serial}})</span></h4>
                            <h4 v-else-if="employee.waiting"><span class="badge badge-warning"><i class="fa fa-clock"></i>&nbsp;Esperando... Por favor escanee la tarjeta RFID</span></h4>
                            <h4 v-else><span class="badge badge-primary"><i class="fa fa-times"></i>&nbsp;Sin Asignar</span></h4>
                        </td>
                        <td>
                            <button @click="removeRfidCard(employee.rfid_serial)" v-if="employee.rfid_serial" class="btn btn-danger">Quitar</button>
                            <button v-else-if="employee.waiting" @click="cancelWaitingForPairing" class="btn btn-warning">Cancelar</button>
                            <button @click="assignRfidCard(employee)" v-else class="btn btn-info">Asignar Tarjeta</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="js/vue.min.js"></script>
<script src="js/vue-toasted.min.js"></script>
<script>
    Vue.use(Toasted);
    let shouldCheck = true;
    const CHECK_PAIRING_EMPLOYEE_INTERVAL = 1000;
    new Vue({
        el: "#app",
        data: () => ({
            employees: [],
            date: "",
        }),
        async mounted() {
            await this.setReaderForReading();
            await this.refreshEmployeesList();
        },
        methods: {
            async removeRfidCard(rfidSerial) {
                await fetch("./remove_rfid_card.php?rfid_serial=" + rfidSerial);
                this.$toasted.show("Se quito el RFID ", {
                    position: "top-left",
                    duration: 1000,
                });
                await this.refreshEmployeesList();
            },
            async cancelWaitingForPairing() {
                shouldCheck = false;
                await this.setReaderForReading();
            },
            async setReaderForReading() {
                await fetch("./set_reader_for_reading.php");
            },
            async assignRfidCard(employee) {
                shouldCheck = true;
                const employeeId = employee.id;
                employee.waiting = true;
                await fetch("./set_reader_for_pairing.php?employee_id=" + employeeId);
                this.checkIfEmployeeHasJustAssignedRfid(employee);
            },
            async checkIfEmployeeHasJustAssignedRfid(employee) {
                const r = await fetch("./get_employee_rfid_serial_by_id.php?employee_id=" + employee.id);
                const serial = await r.json();
                if (!shouldCheck) {
                    employee.waiting = false;
                    return;
                }
                if (serial) {
                    this.$toasted.show("RFID asignado!", {
                        position: "top-left",
                        duration: 1000,
                    });
                    await this.setReaderForReading();
                    await this.refreshEmployeesList();
                } else {
                    setTimeout(() => {
                        this.checkIfEmployeeHasJustAssignedRfid(employee);
                    }, CHECK_PAIRING_EMPLOYEE_INTERVAL);
                }
            },
            async refreshEmployeesList() {
                // Get all employees
                let response = await fetch("./get_employees_ajax.php");
                let employees = await response.json();
                // Set rfid_serial by default: null
                let employeeDictionary = {};
                employees = employees.map((employee, index) => {
                    employeeDictionary[employee.id] = index;
                    return {
                        id: employee.id,
                        name: employee.name,
                        rfid_serial: null,
                        waiting: false,
                    }
                });
                // Get RFID data, if any
                response = await fetch(`./get_employees_with_rfid.php`);
                let rfidData = await response.json();
                // Refresh rfid data in each employee, if any
                rfidData.forEach(rfidDetail => {
                    let employeeId = rfidDetail.employee_id;
                    if (employeeId in employeeDictionary) {
                        let index = employeeDictionary[employeeId];
                        employees[index].rfid_serial = rfidDetail.rfid_serial;
                    }
                });
                // Let Vue do its magic ;)
                this.employees = employees;
            }
        },
    });
</script>
<?php
include_once "footer.php";
