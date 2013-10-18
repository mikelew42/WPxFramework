;(function($){
	var isReady = false;

	$(document).ready(function(){
		isReady = true;
	});

	window.docReady = function(){
		return isReady;
	};

	$(document).ready(function(){
		wpxDebugInit();
	});

	var wpxDebugInit = window.wpxDebugInit = function ($context){
		if (!$context)
			$context = $(document);

		// this should work, but on reinitialization, it will create a new closure, and resave all of these variables...?
		// i'm not 100% sure it does, its an anonymous function.  BUT, because the call back refers to the private var, it probably saves the closure.
		$('.wpx-item', $context).each(function(){
			if ($(this).data('wpxDebugInit'))
				return true;

			var $item = $(this),
				$preview = $item.children('.wpx-item-preview'),
				$content = $item.children('.wpx-item-content'),
				clickHandler = function(){
					$content.slideToggle();
					return false;
				};

			$preview.off('click', clickHandler).on('click', clickHandler);

			$item.data('wpxDebugInit', true);
		});
	}
})(jQuery);

;(function($){

	$(document).ready(function(){

	});

	var jLog = window.jLog = function(file, line, label, args, context){
		var logItem = new LogItem({
			file: file,
			line: line,
			label: label,
			value: args,
			context: context,
			stack: (new Error).stack
		});

	///	jLogger.open(logItem);
	};
	var jLogValue = window.jLogValue = function(file, line, label, value, context){
		var logItem = new LogItem({
			file: file,
			line: line,
			label: label,
			value: value,
			context: context,
			stack: (new Error).stack
		});

		jLogger.add(logItem);
	};
	var jLogOpen = window.jLogOpen = function(file, line, label, value, context){
		var logItem = new LogItem({
			file: file,
			line: line,
			label: label,
			value: value,
			context: context,
			stack: (new Error).stack,
			open: true
		});
		jLogger.add(logItem);
	};
	var jLogEnd = window.jLogEnd = function(file, line, label, args, context){
		//jLogValue(file, line, label, args, context);
		jLogger.end();
	};

	var LogItemView = Backbone.View.extend({
		initialize: function(){
			this.listenTo(this.model, 'change', this.render);
			this.render();
		},
		render: function(){
			if (!docReady()){
				if (!this.renderQueued){
					this.renderQueued = true;
					$(document).ready($.proxy(this.render, this));
				}
				return false;
			} else {
				// kind of hacky way to add the template
				if (!this.$el.hasClass('j-log-item'))
					this.setElement(    $( _.template( $('#j-log-item-template').html() )() )    );

				this.$el.find('.j-log-item-title').html(this.model.get('contextLabel') + " " + this.model.get('file') + ":" + this.model.get('line') + ": " + this.model.get('label') + " === " +  this.model.get('value'));

				if (typeof this.model.get('value') === 'object')
					this.$el.find('.j-log-item-content').html('test content for object');

				if (this.model.get('open'))
					this.$el.addClass('j-log-open');

				if (this.model.get('parent')){
					//console.log('render parent id: ' + this.model.get('parent').cid);
					var parentContent;
					if (parentContent = this.model.get('parent').view.$el.children('.j-log-item-content')){
						parentContent.append(this.$el);
						wpxDebugInit(parentContent);
					}
				}
			}
		}
	});


	var Log = Backbone.Collection.extend({
		model: LogItem,
		currentId: 0, // cid of current item

		/**
		 * The current log item is the target for new log items as they are added.  This method returns the current
		 * log item.
		 * @returns LogItem
		 */
		current: function(){
			// can i prevent these from being recursive?
			//jLog('debug.js', 101, '.current() args', arguments, this);
			//jLogEnd('debug.js', 102, 'return this.get(this.currentId);', { "this.currentId": this.currentId, "this.get(this.currentId)": this.get(this.currentId), "return": this.get(this.currentId) }, this);
			//console.log(this.currentId);
			//console.log(this.get(this.currentId));

			//return false;
			return this.currentId ? this.get(this.currentId) : false;
		}
	});

/*
This method of making private constructors is kinda dumb - then nobody can use them outside of this enclosure.
 */
	var LogItem = Backbone.Model.extend({
		defaults: { parent: null, open: false},
		children: null, // Log collection of children
		logger: null, // whether to open this item as a container, which REQUIRES a matching END
		initialize: function(){
			this.children = new Log();
			this.view = new LogItemView({ model: this});

			var context = this.get('context');
			if (typeof context === "object" ){
				if (context.name)
					this.set('contextLabel', context.name);
				else
					this.set('contextLabel', 'no context');

			}
		},
		currentChild: function(){
			return this.children ? this.children.current() : false ;
		},
		add: function(logItem){
			var cur;
			if (cur = this.currentChild())
				cur.add(logItem);
			else
				this.addNewChild(logItem);
		},
		addNewChild: function(logItem){
			logItem.set('parent', this);

			this.children.add(logItem);

			if (logItem.get('open'))
				this.children.currentId = logItem.cid;
		},
		render: function(){
			console.log('LogItem.render()');
			this.view.render();
		},
		end: function(){  // important note:  This model of a 'deeper' method, that potentially goes deeper on every call, has a lot of contingencies.
			var cur;
			if (cur = this.currentChild()){
				cur.end();
				//cur.render();
			} else {
				this.children.currentId = 0;
				//this.render();
			}
		}
	});

	var LoggerView = Backbone.View.extend({
		initialize: function(){

		},
		render: function(){
			if (!this.$el.hasClass('j-logger'))
				this.setElement(    $( _.template( $('#j-logger-template').html() )() )    );

			$('#j-logger-wrap').append(this.$el);
		}
	});
	/*
	Change Logger to std Model with log property that is a LogItem
	Merge add/open methods and use logItem.open = false (default) property
	Use a generic End method, but can optionally take a logItem.  Just like it is currently
	 */
// Logger could extend jLogItem to avoid duplicating methods and to add functionality
	var Logger = Backbone.Model.extend({
		defaults: {},
		initialize: function(){
			// find a way to extend Backbone to make this default functionality?
			$.extend(this, this.get('root'));

			// This holds all the log items
			this.log = new LogItem({
				root: { logger: this},
				file: '',
				line: '',
				label: 'Root jLogger',
				contextLabel: ''
			});

			// This is the container $el for the entire log widget
			this.view = new LoggerView({ model: this });

			// render the main view on doc.ready
			var logger = this;
			$(document).ready(function(){
				logger.view.render();
				logger.log.view.$el.appendTo(logger.view.$el);
				wpxDebugInit();
			});

			this.debug = true;
		},
		add: function(logItem){
			this.debug && console.log('Logger.add()');
			logItem.logger = this;
			this.log.add(logItem);
		},
		end: function(){
			this.log.end();
		}
	});

	var jLogger = window.jLogger = new Logger();

})(jQuery);
