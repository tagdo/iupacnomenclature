import datetime

project = 'IUPAC Nomenclature'
copyright = f'{datetime.datetime.now().year}, Ayhan Koyun'
author = 'Ayhan Koyun'

extensions = ['sphinx.ext.autodoc', 'sphinx.ext.viewcode', 'sphinx.ext.napoleon']
source_suffix = '.rst'
master_doc = 'index'
language = 'en'
html_theme = 'sphinx_rtd_theme'

html_static_path = ['_static']