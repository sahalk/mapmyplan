<template>
    <div>
        <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
            <!-- Application Logo -->
            <div>
                <jet-application-logo class="block h-12 w-auto" />
            </div>

            <div class="mt-8 text-2xl">
                Sydney!
            </div>

            <div class="mt-6 text-gray-500">
                Watch whales migrate along the coast. See a show at the Sydney Opera House. Climb the Sydney Harbour Bridge with BridgeClimb Sydney.
                Swim in the turquoise waters of Bondi Beach. Learn about Aboriginal culture on tours of Sydney Harbour.
                Catch a ferry and cruise Sydney Harbour to Manly. Enjoy big events such as the spectacular New Year’s Eve fireworks on Sydney Harbour,
                the Sydney Festival, Vivid Sydney, and Mardi Gras. Savour delicious fresh seafood at quality restaurants.
            </div>
        </div>

        <!-- Filter Options -->
        <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25">
            <div class="p-6">
                <div class="flex items-center">
                    <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-8 h-8 text-gray-500"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold"><a href="https://laravel.com/docs">Filter Options</a></div>
                </div>

                <div class="ml-12">
                    <div class="mt-2 text-sm text-gray-500">
                        The data is fetched using Guzzle from Atlas API in JSON format. The data is formatted in the backend (PHP) and then sent to the frontend (Vue). The results can be filtered using Area and/or Suburb.
                    </div>

                    <div class="mt-3 flex items-center text-sm font-semibold text-indigo-700">
                            <div class="mt-4 mr-10">
                                <jet-label for="area" value="Select Area" />
                                <select id="area" class="mt-1 block w-full rounded-md border-gray-300" v-model="current_area" v-on:change="filter()">
                                    <option v-for="area in areas" :key="area" :value="area">{{area}}</option>
                                </select>
                            </div>

                            <div class="mt-4">
                                <jet-label for="suburb" value="Select Suburb" />
                                <select id="suburb" class="mt-1 block w-full rounded-md border-gray-300" v-model="current_suburb" v-on:change="filter()">
                                    <option v-for="suburb in suburbs" :key="suburb" :value="suburb">{{suburb}}</option>
                                </select>
                            </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Load More Accomodations -->
        <div v-if="products.length">
            <vue-element-loading :active="loading" color="rgb(77 70 228)" spinner="spinner" />
            <div v-for="accom in products" :key="accom.productId" class="p-6 sm:px-40 bg-white border-b border-gray-200">
                <div class="w-auto pr-12 inline-block">
                    <img :src="accom.productImage" />
                </div>
                <div class="inline-block align-top h-56">
                    <div class="text-xl font-bold mb-4">
                        {{accom.productName}}
                    </div>

                    <div class="text-gray-500 mb-8">
                        <p>{{accom.addresses[0].address_line}}</p>
                        <p>{{accom.addresses[0].city}}</p>
                        <p>{{accom.addresses[0].state}} - {{accom.addresses[0].postcode}}</p>
                        <p>{{accom.addresses[0].country}}</p>
                    </div>

                    <div class="mt-3" >
                        <button type="submit" class="inline-flex items-center justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" @click="show_modal = true, modal_title = accom.productName, modal_desc = accom.productDescription" >
                        More Info
                        </button>
                    </div>
                </div>
                <div class="float-right">
                    <p>{{accom.status}}</p>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="p-6 sm:px-40 bg-white border-b border-gray-200">
                <p>Sorry, no results found.</p>
            </div>
        </div>
        <div class="p-6 sm:px-40 bg-gray-200 bg-opacity-25">
            <div>
                <button v-if="current_count < total_count" type="submit" class="inline-flex items-center justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" @click="filter(1)">
                    Load More
                </button>
                <span v-else class="-mt-7 pr-3 text-gray-500 italic">
                    End of results
                </span>
            </div>
            <span class="float-right -mt-7 pr-3 text-gray-500 italic">
                Displaying {{current_count}} of {{total_count}} Accomdations
            </span>
        </div>

        <!-- Modal View -->
        <jet-dialog-modal :show="show_modal" @close="closeModal">
            <template #title>
                {{modal_title}}
            </template>

            <template #content>
                {{modal_desc}}

            </template>

            <template #footer>
                <jet-button @click="closeModal">
                    Close
                </jet-button>
            </template>
        </jet-dialog-modal>
    </div>
</template>

<script>
    import JetApplicationLogo from '@/Jetstream/ApplicationLogo'
    import JetLabel from '@/Jetstream/Label'
    import JetButton from './Button'
    import JetDialogModal from './DialogModal'
    import VueElementLoading from "vue-element-loading"

    export default {
        components: {
            JetApplicationLogo,
            JetLabel,
            JetButton,
            JetDialogModal,
            VueElementLoading,
        },
        props: ['areas', 'suburbs', 'accoms', 'count'],

        // All the data required for the page
        data() {
            return {
                loading: false,
                page: 1,
                current_count: (this.count < 10) ? this.count : 10,
                total_count: this.count,
                current_area: 'All Areas',
                current_suburb: 'All Suburbs',
                products: this.accoms,
                show_modal: false,
                modal_title: '',
                modal_desc: '',
            }
        },

        // All the methods used
        methods: {
            // Function to filter the results based on users parameters
            filter(loadMore = 0){
                // Toggle Loading Spinner
                this.loading = true;

                // Change Page Number
                if(loadMore) {
                    this.page++;
                } else {
                    this.page = 1;
                }

                // GET request (using axios) to fetch the accomodation options
                axios.get(this.route('filter',{ area: this.current_area, suburb: this.current_suburb, page_number: this.page }))
                .then(res => {
                    if(res.data.status) {
                        // If user clicks on load more.
                        if(loadMore) {
                            this.products = this.products.concat(res.data.accoms.products);
                            this.current_count = this.current_count + res.data.accoms.products.length
                        // If user uses the filter
                        } else {
                            this.products = res.data.accoms.products;
                            this.total_count = res.data.accoms.numberOfResults;
                            this.current_count = (this.total_count < 10) ? this.total_count : 10;
                            if(this.total_count === 0) {
                                this.$toast.error(`No Accomdations Found.`, { position: "top-right" });
                                this.loading = false;
                                return;
                            }
                        }
                        this.$toast.success(`Accomdations Fetched`, { position: "top-right" });
                    } else {
                        this.$toast.error(`Something went wrong.`, { position: "top-right" });
                    }
                // Toggle Loading Spinner
                this.loading = false;
                });
            },
            // Fucntion to close the modal window
            closeModal() {
                this.show_modal = false
            },
        }
    }
</script>
