import axios from 'axios'
import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    state:{
        user: null,
        profile: null,
        roles: null
    },
    mutations:{
        setUser(state, payload) {
            state.user = payload
        },
        setProfile(state, payload) {
            state.profile = payload
        },
        setRoles(state, payload) {
            state.roles = payload
        }
    },
    actions:{
        async getUser(context) {
            try {
                let url = `/user`
                const responses = await axios.get(url);
                if(responses.data) {
                    const user = responses.data
                    context.commit('setUser', user);
                }                
            } catch (error) {
                console.log(error)
            }
        },
        async getProfile(context) {
            try {
                let url = `/profile/show`
                const responses = await axios.get(url);
                if(responses.data) {
                    const profile = responses.data
                    context.commit('setProfile', profile);
                }                
            } catch (error) {
                console.log(error)
            }
        },
        async getRoles(context) {
            try {
                let url = `/admin/roles`
                const responses = await axios.get(url);
                if(responses.data) {
                    const roles = responses.data
                    context.commit('setRoles', roles);
                }                
            } catch (error) {
                console.log(error)
            }
        },
    },
    getters: {
        getRoleUser(state) {
            if(state.user) {
                return state.user.role_id
            }
        },
        getNameUser(state) {
            if(state.user) {
                return state.user.name
            }
        },
        getIdUser(state) {
            if(state.user) {
                return state.user.id
            }
        },
        getEmailUser(state) {
            if(state.user) {
                return state.user.email
            }
        }
    },
    modules:{}
})