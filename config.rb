Time.zone = "US/Eastern"

###
# Compass
###

# Change Compass configuration
# compass_config do |config|
#   config.output_style = :compact
# end

###
# Page options, layouts, aliases and proxies
###

# Per-page layout changes:
#
# With no layout
# page "/path/to/file.html", :layout => false
#
# With alternative layout
# page "/path/to/file.html", :layout => :otherlayout
#
# A path which all have the same layout
# with_layout :admin do
#   page "/admin/*"
# end

page "/feed.xml", layout: false
page "/404.html", :layout => false
page "/sitemap.xml", :layout => false

# Proxy pages (http://middlemanapp.com/basics/dynamic-pages/)
# proxy "/this-page-has-no-template.html", "/template-file.html", :locals => {
#  :which_fake_page => "Rendering a fake page with a local variable" }

data.work.clients.each do |client|
  proxy "/work/#{client.url}.html", "/work-template.html", locals: { 
    client: client
  }, :ignore => true
end

###
# Helpers
###

# Automatic image dimensions on image_tag helper
# activate :automatic_image_sizes

# Reload the browser automatically whenever files change
configure :development do
  activate :livereload, :host => "thisiscapra.dev", :apply_js_live => true, :apply_css_live => true
  config[:file_watcher_ignore] += [ /^build\// ]
end

# Methods defined in the helpers block are available in templates
helpers do
  # Set the page title
  def page_title
    if content_for?(:title)
      "#{yield_content(:title)} - Capra Design"
    else
      "Capra Design"
    end
  end
  # Set the page description
  def page_description
    if content_for?(:description)
      "#{yield_content(:description)}"
    else
      "Capra is a design agency based in Ottawa, Canada run by husband and wife team Ollie and Kat Kavanagh. Our focus is great design. We love interactive work like websites, games and apps because we get involved in building what we design."
    end
  end
  # Active nav items
  def nav_active(page)
    current_page.url.start_with?(page) ? {:class => 'active'} : {}
  end
  # Custom page classes
  def custom_page_classes
    "page-#{page_classes} #{yield_content(:page_class) if content_for?(:page_class)}"
  end
  # Tag lists
  def sentence_tag_list(article)
    if tags = article.tags
      content_tag(:div, class: :tags) do
        "This article was filed under " +
        article.tags.map{|t| link_to t, "/tags/#{t}"}.to_sentence +
        "."
      end
    end
  end
  # Pretty dates
  def pretty_date(date)
    date.strftime('%B %d, %Y')
  end
  # Check if page
  def page(path)
    current_page.path == path
  end
end

activate :directory_indexes

set :css_dir, 'stylesheets'

set :js_dir, 'javascripts'

set :images_dir, 'images'

# Build-specific configuration
configure :build do
  # For example, change the Compass output style for deployment
  activate :minify_css

  # Minify Javascript on build
  activate :minify_javascript

  # Enable cache buster
  activate :asset_hash

  # Use relative URLs
  activate :relative_assets

  # Or use a different image path
  # set :http_prefix, "/Content/images/"
end
