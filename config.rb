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

#page "/feed.xml", layout: false
page "/404.html", :layout => false
page "/sitemap.xml", :layout => false

# Proxy pages (http://middlemanapp.com/basics/dynamic-pages/)
# proxy "/this-page-has-no-template.html", "/template-file.html", :locals => {
#  :which_fake_page => "Rendering a fake page with a local variable" }

app.data.work.clients.each do |client|
  proxy "/work/#{client.url}/index.html", "/work-template.html", locals: { 
    client: client
  }, :ignore => true
end

###
# Helpers
###

# Automatic image dimensions on image_tag helper
# activate :automatic_image_sizes

# Methods defined in the helpers block are available in templates
helpers do
  # Set the page title
  def page_title
    if content_for?(:title)
      "#{strip_tags(yield_content(:title))}"
    else
      "OK Design"
    end
  end
  # Set the page description
  def page_description
    if content_for?(:description)
      "#{yield_content(:description)}"
    else
      "Hi, I'm Ollie, a creative designer and front end developer currently residing in Ottawa, Canada."
    end
  end
  # Active nav items
  def nav_active(path)
    current_page.path === path ? { class: 'active' } : {}
  end
  # Custom page classes
  def custom_page_classes
    "class='page-#{page_classes} #{yield_content(:page_class) if content_for?(:page_class)}'"
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
  # Content tag blocks breaking
  def FixedContentHelper
    def content_tag(name, content, options, &block)
      content = content.join('') if content.respond_to?(:each)
      super(name, content, options, &block)
    end
  end
  # Inline SVGs
  def svg(name)
    root = Middleman::Application.root
    file_path = "#{root}/source/images/#{name}.svg"
    return File.read(file_path) if File.exists?(file_path)
    '(not found)'
  end
end

activate :directory_indexes

activate :sprockets

# Build-specific configuration
configure :build do
  # Minify CSS on build
  activate :minify_css

  # Minify Javascript on build
  activate :minify_javascript

  # Enable cache buster
  activate :asset_hash

  activate :gzip

  # Use relative URLs
  # activate :relative_assets
  
end
