import { asyncRoutes, constantRoutes } from '@/router'
import Layout from '@/layout'

function generateTree(list, $pid = 0) {
  const tree = []
  for (let i = 0; i < list.length; i++) {
    const item = list[i]
    if (item.pid === $pid) {
      const tmp = {
        path: item.path,
        name: item.label || '',
        meta: {
          title: item.name || '',
          icon: item.icon ? 'el-icon-' + item.icon : ''
        },
        hidden: !!+item.hidden
      }

      if ($pid === 0) {
        tmp.component = Layout
      } else {
        tmp.component = (resolve) => require([`@/views/${item.component}`], resolve)
      }
      const children = generateTree(list, item['id'])

      if (children.length > 0) {
        tmp.children = children
      }
      tree.push(tmp)
    }
  }

  return tree
}

/**
 * Use meta.role to determine if the current user has permission
 * @param roles
 * @param route
 */
function hasPermission(roles, route) {
  if (route.meta && route.meta.roles) {
    return roles.some(role => route.meta.roles.includes(role))
  } else {
    return true
  }
}

/**
 * Filter asynchronous routing tables by recursion
 * @param routes asyncRoutes
 * @param roles
 */
export function filterAsyncRoutes(routes, roles) {
  const res = []

  routes.forEach(route => {
    const tmp = { ...route }
    if (hasPermission(roles, tmp)) {
      if (tmp.children) {
        tmp.children = filterAsyncRoutes(tmp.children, roles)
      }
      res.push(tmp)
    }
  })

  return res
}

const state = {
  routes: [],
  addRoutes: []
}

const mutations = {
  SET_ROUTES: (state, routes) => {
    state.addRoutes = routes
    state.routes = constantRoutes.concat(routes)
  }
}

const actions = {
  generateRoutes({ commit }, routes) {
    return new Promise(resolve => {
      const accessedRoutes = generateTree(routes).concat(asyncRoutes)
      commit('SET_ROUTES', accessedRoutes)
      resolve(accessedRoutes)
    })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
