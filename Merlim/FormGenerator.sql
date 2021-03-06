USE [merlim_d2_net_br]
GO
/****** Object:  Table [dbo].[sipTabela]    Script Date: 09/04/2010 11:14:51 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[sipTabela](
	[tabCodigo] [int] IDENTITY(1,1) NOT NULL,
	[tabNome] [varchar](255) NULL,
	[tabResumo] [varchar](255) NULL,
	[tabTitle] [varchar](255) NULL,
	[tabAlterado] [tinyint] NULL CONSTRAINT [DF_sipTabela_loaAlterado]  DEFAULT ((1)),
	[tabAtivo] [tinyint] NOT NULL CONSTRAINT [DF_sipTabela_loaAtivo]  DEFAULT ((1)),
	[tabAlteracao] [datetime] NULL,
	[tabInclusao] [datetime] NULL CONSTRAINT [DF_sipTabela_tabInclusao]  DEFAULT (getdate()),
	[tabExclusao] [datetime] NULL,
 CONSTRAINT [PK_sipTabela] PRIMARY KEY CLUSTERED 
(
	[tabCodigo] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[sipTabelaFieldset]    Script Date: 09/04/2010 11:14:39 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[sipTabelaFieldset](
	[tfsCodigo] [int] IDENTITY(1,1) NOT NULL,
	[tfsTabela] [int] NULL,
	[tfsID] [tinyint] NULL,
	[tfsTitle] [varchar](255) NULL,
	[tfsLegend] [varchar](255) NULL,
	[tfsAlterado] [tinyint] NULL CONSTRAINT [DF_sipTabelaFieldset_tfsAlterado]  DEFAULT ((1)),
	[tfsInclusao] [datetime] NULL CONSTRAINT [DF_sipTabelaFieldset_tfsInclusao]  DEFAULT (getdate()),
	[tfsAlteracao] [datetime] NULL,
 CONSTRAINT [PK_sipTabelaFieldset] PRIMARY KEY CLUSTERED 
(
	[tfsCodigo] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[sipTabelaFK]    Script Date: 09/04/2010 11:14:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[sipTabelaFK](
	[tafCodigo] [int] IDENTITY(1,1) NOT NULL,
	[tafTabela] [int] NULL,
	[tafColuna] [varchar](50) NULL,
	[tafTable] [varchar](50) NULL,
	[tafSelect] [varchar](100) NULL,
	[tafWhere] [varchar](100) NULL,
	[tafOrderBy] [varchar](100) NULL,
	[tafInclusao] [datetime] NULL CONSTRAINT [DF_sipTabelaFK_tafInclusao]  DEFAULT (getdate()),
 CONSTRAINT [PK_sipTabelaFK] PRIMARY KEY CLUSTERED 
(
	[tafCodigo] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[sipTabelaCampo]    Script Date: 09/04/2010 11:14:47 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[sipTabelaCampo](
	[tacCodigo] [int] IDENTITY(1,1) NOT NULL,
	[tacTabela] [int] NOT NULL,
	[tacUsed] [tinyint] NULL,
	[tacOrder] [varchar](10) NULL,
	[tacFieldset] [tinyint] NULL,
	[tacNome] [varchar](50) NULL,
	[tacLabel] [varchar](50) NULL,
	[tacType] [tinyint] NULL,
	[tacSize] [int] NULL,
	[tacMaxSize] [int] NULL,
	[tacRows] [tinyint] NULL,
	[tacValidation] [tinyint] NULL,
	[tacRequired] [tinyint] NULL,
	[tacReadOnly] [tinyint] NULL CONSTRAINT [DF_sipTabelaCampo_tacReadonly]  DEFAULT ((0)),
	[tacTitle] [varchar](255) NULL,
	[tacFind] [tinyint] NULL,
	[tacList] [tinyint] NULL,
	[tacListOrder] [tinyint] NULL,
	[tacBR] [tinyint] NULL,
	[tacInclusao] [datetime] NULL CONSTRAINT [DF_sipTabelaCampo_tacInclusao]  DEFAULT (getdate()),
 CONSTRAINT [PK_sipTabelaCampo_1] PRIMARY KEY CLUSTERED 
(
	[tacCodigo] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  ForeignKey [FK_sipTabelaFK_sipTabela]    Script Date: 09/04/2010 11:14:35 ******/
ALTER TABLE [dbo].[sipTabelaFK]  WITH CHECK ADD  CONSTRAINT [FK_sipTabelaFK_sipTabela] FOREIGN KEY([tafTabela])
REFERENCES [dbo].[sipTabela] ([tabCodigo])
GO
ALTER TABLE [dbo].[sipTabelaFK] CHECK CONSTRAINT [FK_sipTabelaFK_sipTabela]
GO
/****** Object:  ForeignKey [FK_sipTabelaFieldset_sipTabela]    Script Date: 09/04/2010 11:14:39 ******/
ALTER TABLE [dbo].[sipTabelaFieldset]  WITH CHECK ADD  CONSTRAINT [FK_sipTabelaFieldset_sipTabela] FOREIGN KEY([tfsTabela])
REFERENCES [dbo].[sipTabela] ([tabCodigo])
GO
ALTER TABLE [dbo].[sipTabelaFieldset] CHECK CONSTRAINT [FK_sipTabelaFieldset_sipTabela]
GO
/****** Object:  ForeignKey [FK_sipTabelaCampo_sipTabela]    Script Date: 09/04/2010 11:14:47 ******/
ALTER TABLE [dbo].[sipTabelaCampo]  WITH CHECK ADD  CONSTRAINT [FK_sipTabelaCampo_sipTabela] FOREIGN KEY([tacTabela])
REFERENCES [dbo].[sipTabela] ([tabCodigo])
GO
ALTER TABLE [dbo].[sipTabelaCampo] CHECK CONSTRAINT [FK_sipTabelaCampo_sipTabela]
GO
