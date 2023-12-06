<template>

    <div>
        <div class="card booking-schedule schedule-widget">
            <div class="schedule-header">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Day Slot -->
                        <div class="day-slot">
                            <ul>
                                <li class="left-arrow" v-if="canGoPrevious">
                                    <a href="" @click.prevent="getPreviousWeek" :class="{disabled:!canGoPrevious}">
                                        <i class="fa fa-chevron-left"></i>
                                    </a>
                                </li>
                                <li v-for="(schedule,index) in schedules">
                                    <span v-html="schedule.short_day_name"></span>
                                    <span class="slot-date" v-html="schedule.label"></span>
                                </li>
                                <li class="right-arrow">
                                    <a href="">
                                        <i class="fa fa-chevron-right" @click.prevent="getNextWeek"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- /Day Slot -->
                    </div>
                </div>
            </div>
            <div class="schedule-cont">
                <div class="row">
                    <div class="col-md-12">
                        <div class="time-slot">
                            <ul class="clearfix">
                                <li v-for="(schedule,dIndex) in schedules" :key="'day_'+dIndex">

                                    <template v-for="(slot,sIndex) in schedule.slots">

                                        <label v-if="slot.status=='selected'" class="timing selected">
                                            <a href="#"
                                               class="cancel-selected"
                                               :key="'slot_'+sIndex"
                                               @click.prevent="showOptionalLineItemModal(slot)">
                                            </a>

                                            {{slot.label}}
                                        </label>


                                        <a class="timing" href="#" v-else
                                           :key="'slot_'+sIndex"
                                           :class="slot.status"
                                           @click.prevent="showOptionalLineItemModal(slot)">
                                            <span v-html="slot.label"></span>
                                        </a>


                                    </template>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="row mb-1"  id="proceed_to_checkout">
            <div class="col-md-12 text-right" v-if="selectedReservationHashedId">
                <a class="btn btn-success" :href="`/reserve/checkout/${this.selectedReservationHashedId}`">
                    <i class="fas fa-shopping-cart"></i> {{getLabels.proceed_to_checkout}}
                </a>
            </div>
        </div>

        <transition name="fade">
            <div v-if="showModal">
                <div class="modal-mask">
                    <div class="modal-wrapper">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title" v-html="modalHeaderLabel"></h6>
                                    <button type="button" class="close" @click="showModal=false">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">

                                    <div v-if="optionalLineItems.length">

                                        <h4 v-html="getLabels.lineItemsModalHeader"></h4>
                                        <div v-for="(optionalLineItem,lIndex) in optionalLineItems"
                                             :key="'line_' + lIndex" class="ml-4">
                                            <input type="checkbox" :value="optionalLineItem.code"
                                                   v-model="selectedOptionalLineItems"
                                                   :id="optionalLineItem.code"
                                                   style="cursor: pointer"
                                                   :name="optionalLineItem.code">
                                            <label :for="optionalLineItem.code" style="cursor: pointer">
                                                {{ optionalLineItem.description }}
                                                ({{ optionalLineItem.rate_value | money }}) </label>
                                        </div>
                                    </div>

                                    <div v-else>
                                        <h4 class="text-center" v-html="getLabels.emptyOptionalLineItemsMessage"></h4>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-success" @click.prevent="createReservation"
                                            v-html="getLabels.confirm">
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
        <loading :active.sync="isLoading" :is-full-page="true" loader="dots"></loading>
    </div>
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';

    export default {
        name: "Schedule",

        props: ['serviceHashedId'],
        components: {
            Loading
        },
        data() {
            return {
                schedules: [],
                dateFormat: 'YYYY-MM-DD',
                previousWeekDate: null,
                nextWeekDate: moment().format('YYYY-MM-DD'),
                showModal: false,
                serviceObject: null,
                selectedOptionalLineItems: [],
                starts_at: null,
                ends_at: null,
                optionalLineItems: [],
                selectedReservationHashedId: null
            }
        },
        mounted() {
            this.getSchedule(this.nextWeekDate);
        },
        methods: {
            createReservation() {
                let lineItems = [];

                this.selectedOptionalLineItems.forEach((lineItemCode) => {
                    lineItems.push({code: lineItemCode});
                });

                axios.post(`reserve/create-reservation`, {
                    starts_at: this.starts_at,
                    ends_at: this.ends_at,
                    line_items: lineItems,
                    service_id: this.serviceObject.id,
                    selected_reservation_hashed_id: this.selectedReservationHashedId
                }).then(({data}) => {
                    this.selectedReservationHashedId = data.selectedReservationId;
                    this.refreshSchedule();
                    this.showModal = false;
                    this.$scrollTo('#proceed_to_checkout', 2000)
                }).catch(({response}) => {
                    themeNotify({
                        level: 'error',
                        message: response.data.message
                    })
                });
            },
            async showOptionalLineItemModal(slot) {

                if (slot.status == 'selected') {
                    await this.removeSelectedReservationAndRedrawSchedule();
                    return;
                }

                if (slot.status === 'reserved') {
                    return;
                }

                this.starts_at = slot.starts_at;
                this.ends_at = slot.ends_at;
                this.optionalLineItems = await this.getOptionalLineItems();

                this.showModal = true;
            },

            async removeSelectedReservationAndRedrawSchedule() {

                themeConfirmation(
                    corals.confirmation.title,
                    corals.confirmation.delete.text,
                    'warning',
                    corals.confirmation.delete.yes,
                    corals.confirmation.cancel,
                    async () => {
                        await axios.post(`reserve/remove-reservation/${this.selectedReservationHashedId}`)
                            .then(this.refreshSchedule);
                    });


            },

            refreshSchedule() {
                this.getSchedule(this.schedules[0].date)
            },

            async getOptionalLineItems() {

                return await axios.get(
                    `reserve/${this.serviceHashedId}/get-optional-line-items?startsAt=${this.starts_at}&endsAt=${this.ends_at}`
                ).then(({data}) => {
                    return data;
                });
            },

            getPreviousWeek() {
                if (!this.canGoPrevious) {
                    return;
                }
                this.getSchedule(this.previousWeekDate);
            },
            getNextWeek() {
                this.getSchedule(this.nextWeekDate);
            },
            getSchedule(date) {
                axios.get(
                    `reserve/schedule/get-service-schedule/${this.serviceHashedId}?start_date=${date}`
                ).then(({data}) => {
                    this.schedules = data.schedule;
                    this.previousWeekDate = data.pagination.previous;
                    this.nextWeekDate = data.pagination.next;
                    this.serviceObject = data.serviceObject;
                    this.selectedReservationHashedId = data.selectedReservationHashedId
                });
            }
        },
        computed: {
            canGoPrevious() {
                return this.previousWeekDate !== null;
            },
            getLabels() {
                return window.corals.public_js_labels;
            },
            isLoading() {
                return this.$store.getters.getIsLoading;
            },
            modalHeaderLabel() {
                return `${this.getLabels.reserve} <strong style="color: #0a6ebd">
                                        ${moment(this.starts_at).format('DD MMM YYYY, h:mm A')}
                                </strong>
                                           ${this.getLabels.to}
                                <strong style="color: #0a6ebd"> ${moment(this.ends_at).format('DD MMM YYYY, h:mm A')}
                                </strong>`;
            }
        },
        filters: {
            money(value) {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                    minimumFractionDigits: 0
                }).format(value);
            }
        }
    }
</script>

<style scoped>
    .disabled {
        cursor: not-allowed;
    }

    .modal-mask {
        position: fixed;
        z-index: 9998;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
        display: table;
        transition: opacity .10s ease;
    }

    .modal-wrapper {
        display: table-cell;
        vertical-align: middle;
    }

    .reserved {
        cursor: not-allowed;
    }

    .fade-enter-active,
    .fade-leave-active {
        transition: opacity 0.5s
    }

    .fade-enter,
    .fade-leave-active {
        opacity: 0
    }

    .available {
        background-color: white !important;
        color: black !important;
    }

    .available:hover {
        background-color: #2f7fc5 !important;
    }

    .time-slot li .timing.selected::before {
        content: unset;
    }

    .time-slot li .timing.selected .cancel-selected::before {
        content: "\f00d" !important;
        color: #fff;
        font-family: "Font Awesome 5 Free";
        font-size: 12px;
        font-weight: 900;
        position: absolute;
        right: 6px;
        top: 6px;
    }


</style>
