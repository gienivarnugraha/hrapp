export const getParam = (o, searchParam = new URLSearchParams) => {
  Object.entries(o).forEach(([k, v]) => {
    if (v !== null && typeof v === 'object')
      getParam(v, searchParam)
    else
      searchParam.append(k, v)
  })

  return searchParam
}

export const get = async (url, options) => {
  try {
    let query = url

    if (options.value) {
      query += '?' + getParam(options.value)
    }
    const { data } = await axios.get(query)

    return {
      items: reactive(data.data),
      totalItems: data.total,
    }

  } catch (error) {
    console.log(error);
  }

}