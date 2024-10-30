const getImage = (attributes, callback) => {
	const imgAttributes = {
		src: attributes.imageUrl,
		'data-metapic-id': attributes.imageId,
		'data-metapic-tags': attributes.imageTags,
	};
	let linkList = attributes.links.map((link, i) => [
		<a rel="noopener" className="mtpc-link" href={link.url}>{link.content}</a>,
		<span className="mtpc-separator">//</span>
	]);
	if (linkList.length > 0) {
		linkList[linkList.length - 1].pop();
	}
	if (typeof callback === "function") {
		imgAttributes.onClick = () => openTagEditor(attributes, 'tag-editor', callback);
	}

	if (!imgAttributes.src) return null;

	return (
		<div className="mtpc-container">
			<img {...imgAttributes} className="mtpc-image"/>
			<section className="mtpc-links">
				{linkList}
			</section>
		</div>
	);
};
const getTagsFromJSON = (tagData) => {
	return JSON.parse(tagData).map(tag => {
		return {
			url: tag.link,
			content: tag.text,
		};
	});
};
const getBlockAttributes = () => ({
	imageAlt: {
		type: 'string',
		attribute: 'alt',
		selector: 'img',
		default: ''
	},
	imageUrl: {
		type: 'string',
		attribute: 'src',
		selector: 'img',
		default: ''
	},
	imageId: {
		type: 'string',
		attribute: 'data-metapic-id',
		selector: 'img',
		default: ''
	},
	imageTags: {
		type: 'string',
		attribute: 'data-metapic-tags',
		selector: 'img',
		default: ''
	},
	links: {
		type: 'array',
		source: 'query',
		selector: 'a',
		default: [],
		query: {
			url: {
				type: 'string',
				source: 'attribute',
				attribute: 'href',
			},
			content: {
				type: 'string',
				source: 'text',
			},
		}
	}
});

const openTagEditor = (attributes, mode, callback) => {
	jQuery.event.trigger({
		type: 'metapic',
		startPage: mode,
		hideSidebar: true,
		imgSrc: attributes.imageUrl,
		//text:editor.selection.getNode(),
		baseUrl: wp_mtpc.baseUrl,
		randomKey: wp_mtpc.accessToken
	});

	$(document).one('metapicReturn', function (data) {
		console.log("RETURNING", data, callback);
		const returnData = jQuery(data.text);
		const tags = getTagsFromJSON(returnData.attr('data-metapic-tags'));

		callback({
			imageAlt: returnData.attr('alt'),
			imageUrl: returnData.attr('src'),
			imageId: returnData.attr('data-metapic-id'),
			imageTags: returnData.attr('data-metapic-tags'),
			links: tags
		});
	});

	$(document).one('metapicClose', () => {
		console.log("CLOSING");
		$(document).off('metapicReturn');
	});
};

export {getImage, getTagsFromJSON, getBlockAttributes, openTagEditor };
