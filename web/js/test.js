st = StreamTable('#stream_table',
{ view: view, 
  per_page: 10, 
  data_url: BASE_URL+'StreamTable/examples/data/movies.json',
  stream_after: 0.5,
  fetch_data_limit: 100,
  callbacks: callbacks,
  pagination: {span: 5, next_text: 'Next &rarr;', prev_text: '&larr; Previous'}
},
data);